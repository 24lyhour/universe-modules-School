<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\Dashboard\V1\ClassroomController;
use Modules\School\Http\Controllers\Dashboard\V1\CourseController;
use Modules\School\Http\Controllers\Dashboard\V1\DepartmentController;
use Modules\School\Http\Controllers\Dashboard\V1\ProgramController;
use Modules\School\Http\Controllers\Dashboard\V1\SchoolController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    // Schools
    Route::resource('schools', SchoolController::class)->names('schools');
    Route::get('schools/{school}/delete', [SchoolController::class, 'confirmDelete'])->name('schools.delete');
    Route::put('schools/{school}/toggle-status', [SchoolController::class, 'toggleStatus'])->name('schools.toggle-status');

    // Departments
    Route::resource('departments', DepartmentController::class)->names('departments');
    Route::get('departments/{department}/delete', [DepartmentController::class, 'confirmDelete'])->name('departments.delete');
    Route::put('departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
    Route::get('api/departments', [DepartmentController::class, 'getDepartments'])->name('departments.api');

    // Programs
    Route::resource('programs', ProgramController::class)->names('programs');
    Route::get('programs/{program}/delete', [ProgramController::class, 'confirmDelete'])->name('programs.delete');
    Route::put('programs/{program}/toggle-status', [ProgramController::class, 'toggleStatus'])->name('programs.toggle-status');
    Route::get('api/programs', [ProgramController::class, 'getPrograms'])->name('programs.api');

    // Courses
    Route::resource('courses', CourseController::class)->names('courses');
    Route::get('courses/{course}/delete', [CourseController::class, 'confirmDelete'])->name('courses.delete');
    Route::put('courses/{course}/toggle-status', [CourseController::class, 'toggleStatus'])->name('courses.toggle-status');

    // Classrooms
    Route::resource('classrooms', ClassroomController::class)->names('classrooms');
    Route::get('classrooms/{classroom}/delete', [ClassroomController::class, 'confirmDelete'])->name('classrooms.delete');
    Route::put('classrooms/{classroom}/toggle-status', [ClassroomController::class, 'toggleStatus'])->name('classrooms.toggle-status');
});
