<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Modules\Employee\Models\Employee;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'school_departments';

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
        'established_year',
        'total_staff',
        'total_students',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'established_year' => 'integer',
        'total_staff' => 'integer',
        'total_students' => 'integer',
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
}
