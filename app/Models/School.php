<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmployeeType;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'schools';

    /**
     * School types.
     */
    public const TYPE_UNIVERSITY = 'university';
    public const TYPE_INSTITUTE = 'institute';
    public const TYPE_COLLEGE = 'college';
    public const TYPE_SCHOOL = 'school';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'uuid',
        'name',
        'code',
        'type',
        'description',
        'address',
        'city',
        'country',
        'phone',
        'email',
        'website',
        'logo',
        'established_year',
        'accreditation',
        'total_students',
        'total_staff',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'established_year' => 'integer',
        'total_students' => 'integer',
        'total_staff' => 'integer',
        'status' => 'boolean',
    ];

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
    }

    /**
     * Get the departments for the school.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get the programs for the school.
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * Get the employees for the school.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the employee types for the school.
     */
    public function employeeTypes(): HasMany
    {
        return $this->hasMany(EmployeeType::class);
    }

    /**
     * Get the courses through departments.
     */
    public function courses(): HasManyThrough
    {
        return $this->hasManyThrough(Course::class, Department::class);
    }

    /**
     * Get available school types.
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_UNIVERSITY => 'University',
            self::TYPE_INSTITUTE => 'Institute',
            self::TYPE_COLLEGE => 'College',
            self::TYPE_SCHOOL => 'School',
        ];
    }

    /**
     * Scope for active schools.
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
