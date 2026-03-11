<?php

namespace Modules\School\Models;

use App\Traits\BelongsToSchool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Employee\Models\Employee;

class Department extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchool;

    protected $table = 'school_departments';

    /**
     * The model's default values for attributes.
     */
    protected $attributes = [
        'total_students' => 0,
    ];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'school_id',
        'name',
        'code',
        'description',
        'head_of_department',
        'class_room_id',
        'email',
        'phone',
        'office_location',
        'latitude',
        'longitude',
        'geofence_radius',
        'enforce_geofence',
        'timezone',
        'established_year',
        'total_students',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'established_year' => 'integer',
        'total_students' => 'integer',
        'status' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'geofence_radius' => 'integer',
        'enforce_geofence' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['staff_count'];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });

        // Ensure nullable integers have defaults before save
        static::saving(function ($model) {
            if (is_null($model->total_students)) {
                $model->total_students = 0;
            }
        });
    }

    /**
     * Get the school that owns the department.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the programs for the department.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Get the courses for the department.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the employees/staff for the department.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the staff count (computed from employees relationship).
     */
    public function getStaffCountAttribute(): int
    {
        return $this->employees()->count();
    }

    /**
     * Scope for active departments.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Get the classrooms for the department.
     */
    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Check if a location is within the department's geofence.
     *
     * Uses the Haversine formula to calculate distance between two GPS coordinates.
     * Industry standard for geofence verification.
     *
     * @param float|null $lat Latitude to check
     * @param float|null $lng Longitude to check
     * @return array{within: bool, distance: float|null, radius: int}
     */
    public function isWithinGeofence(?float $lat, ?float $lng): array
    {
        // If geofence not configured or not enforced
        if (!$this->latitude || !$this->longitude) {
            return [
                'within' => true,
                'distance' => null,
                'radius' => $this->geofence_radius ?? 100,
                'configured' => false,
            ];
        }

        // If no scan location provided
        if (!$lat || !$lng) {
            return [
                'within' => !$this->enforce_geofence, // Allow if not enforced
                'distance' => null,
                'radius' => $this->geofence_radius,
                'configured' => true,
            ];
        }

        // Calculate distance using Haversine formula
        $distance = $this->calculateDistance($this->latitude, $this->longitude, $lat, $lng);

        return [
            'within' => $distance <= $this->geofence_radius,
            'distance' => round($distance, 2),
            'radius' => $this->geofence_radius,
            'configured' => true,
        ];
    }

    /**
     * Calculate distance between two GPS coordinates using Haversine formula.
     *
     * @param float $lat1 First latitude
     * @param float $lng1 First longitude
     * @param float $lat2 Second latitude
     * @param float $lng2 Second longitude
     * @return float Distance in meters
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Check if department has geofence configured.
     */
    public function hasGeofence(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Get Google Maps URL for the department location.
     */
    public function getGoogleMapsUrl(): ?string
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        return sprintf(
            'https://www.google.com/maps?q=%s,%s',
            $this->latitude,
            $this->longitude
        );
    }
}
