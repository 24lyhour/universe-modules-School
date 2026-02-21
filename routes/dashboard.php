<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\Dashboard\V1\ClassroomController;
use Modules\School\Http\Controllers\Dashboard\V1\CourseController;
use Modules\School\Http\Controllers\Dashboard\V1\DepartmentController;
use Modules\School\Http\Controllers\Dashboard\V1\EquipmentController;
use Modules\School\Http\Controllers\Dashboard\V1\ProgramController;
use Modules\School\Http\Controllers\Dashboard\V1\SchoolController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    // Schools - with permission middleware
    Route::middleware('permission:schools.view_any')->group(function () {
        Route::get('schools', [SchoolController::class, 'index'])->name('schools.index');
    });

    Route::middleware('permission:schools.view')->group(function () {
        Route::get('schools/{school}', [SchoolController::class, 'show'])->name('schools.show');
    });

    Route::middleware('permission:schools.create')->group(function () {
        Route::get('schools/create', [SchoolController::class, 'create'])->name('schools.create');
        Route::post('schools', [SchoolController::class, 'store'])->name('schools.store');
    });

    Route::middleware('permission:schools.update')->group(function () {
        Route::get('schools/{school}/edit', [SchoolController::class, 'edit'])->name('schools.edit');
        Route::put('schools/{school}', [SchoolController::class, 'update'])->name('schools.update');
        Route::patch('schools/{school}', [SchoolController::class, 'update']);
        Route::put('schools/{school}/toggle-status', [SchoolController::class, 'toggleStatus'])->name('schools.toggle-status');
    });

    Route::middleware('permission:schools.delete')->group(function () {
        Route::get('schools/{school}/delete', [SchoolController::class, 'confirmDelete'])->name('schools.delete');
        Route::delete('schools/{school}', [SchoolController::class, 'destroy'])->name('schools.destroy');
    });

    // Departments - with permission middleware
    Route::middleware('permission:departments.view_any')->group(function () {
        Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::get('api/departments', [DepartmentController::class, 'getDepartments'])->name('departments.api');
    });

    Route::middleware('permission:departments.view')->group(function () {
        Route::get('departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
        Route::get('departments/{department}/qr-code', [DepartmentController::class, 'qrCode'])->name('departments.qr-code');
    });

    Route::middleware('permission:departments.create')->group(function () {
        Route::get('departments/create', [DepartmentController::class, 'create'])->name('departments.create');
        Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store');
    });

    Route::middleware('permission:departments.update')->group(function () {
        Route::get('departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
        Route::put('departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::patch('departments/{department}', [DepartmentController::class, 'update']);
        Route::put('departments/{department}/toggle-status', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle-status');
    });

    Route::middleware('permission:departments.delete')->group(function () {
        Route::get('departments/{department}/delete', [DepartmentController::class, 'confirmDelete'])->name('departments.delete');
        Route::delete('departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    });

    // Programs - with permission middleware
    Route::middleware('permission:programs.view_any')->group(function () {
        Route::get('programs', [ProgramController::class, 'index'])->name('programs.index');
        Route::get('api/programs', [ProgramController::class, 'getPrograms'])->name('programs.api');
    });

    Route::middleware('permission:programs.view')->group(function () {
        Route::get('programs/{program}', [ProgramController::class, 'show'])->name('programs.show');
    });

    Route::middleware('permission:programs.create')->group(function () {
        Route::get('programs/create', [ProgramController::class, 'create'])->name('programs.create');
        Route::post('programs', [ProgramController::class, 'store'])->name('programs.store');
    });

    Route::middleware('permission:programs.update')->group(function () {
        Route::get('programs/{program}/edit', [ProgramController::class, 'edit'])->name('programs.edit');
        Route::put('programs/{program}', [ProgramController::class, 'update'])->name('programs.update');
        Route::patch('programs/{program}', [ProgramController::class, 'update']);
        Route::put('programs/{program}/toggle-status', [ProgramController::class, 'toggleStatus'])->name('programs.toggle-status');
    });

    Route::middleware('permission:programs.delete')->group(function () {
        Route::get('programs/{program}/delete', [ProgramController::class, 'confirmDelete'])->name('programs.delete');
        Route::delete('programs/{program}', [ProgramController::class, 'destroy'])->name('programs.destroy');
    });

    // Courses - with permission middleware
    Route::middleware('permission:courses.view_any')->group(function () {
        Route::get('courses', [CourseController::class, 'index'])->name('courses.index');
    });

    Route::middleware('permission:courses.view')->group(function () {
        Route::get('courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    });

    Route::middleware('permission:courses.create')->group(function () {
        Route::get('courses/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('courses', [CourseController::class, 'store'])->name('courses.store');
    });

    Route::middleware('permission:courses.update')->group(function () {
        Route::get('courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
        Route::put('courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::patch('courses/{course}', [CourseController::class, 'update']);
        Route::put('courses/{course}/toggle-status', [CourseController::class, 'toggleStatus'])->name('courses.toggle-status');
    });

    Route::middleware('permission:courses.delete')->group(function () {
        Route::get('courses/{course}/delete', [CourseController::class, 'confirmDelete'])->name('courses.delete');
        Route::delete('courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    });

    // Classrooms - with permission middleware
    Route::middleware('permission:classrooms.view_any')->group(function () {
        Route::get('classrooms', [ClassroomController::class, 'index'])->name('classrooms.index');
    });

    Route::middleware('permission:classrooms.view')->group(function () {
        Route::get('classrooms/{classroom}', [ClassroomController::class, 'show'])->name('classrooms.show');
        Route::get('classrooms/{classroom}/qr-code', [ClassroomController::class, 'qrCode'])->name('classrooms.qr-code');
    });

    Route::middleware('permission:classrooms.create')->group(function () {
        Route::get('classrooms/create', [ClassroomController::class, 'create'])->name('classrooms.create');
        Route::post('classrooms', [ClassroomController::class, 'store'])->name('classrooms.store');
    });

    Route::middleware('permission:classrooms.update')->group(function () {
        Route::get('classrooms/{classroom}/edit', [ClassroomController::class, 'edit'])->name('classrooms.edit');
        Route::put('classrooms/{classroom}', [ClassroomController::class, 'update'])->name('classrooms.update');
        Route::patch('classrooms/{classroom}', [ClassroomController::class, 'update']);
        Route::put('classrooms/{classroom}/toggle-status', [ClassroomController::class, 'toggleStatus'])->name('classrooms.toggle-status');
    });

    Route::middleware('permission:classrooms.delete')->group(function () {
        Route::get('classrooms/{classroom}/delete', [ClassroomController::class, 'confirmDelete'])->name('classrooms.delete');
        Route::delete('classrooms/{classroom}', [ClassroomController::class, 'destroy'])->name('classrooms.destroy');
    });

    // Equipment - with permission middleware
    Route::middleware('permission:equipment.view_any')->group(function () {
        Route::get('equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    });

    Route::middleware('permission:equipment.view')->group(function () {
        Route::get('equipment/{equipment}', [EquipmentController::class, 'show'])->name('equipment.show');
    });

    Route::middleware('permission:equipment.create')->group(function () {
        Route::get('equipment/create', [EquipmentController::class, 'create'])->name('equipment.create');
        Route::post('equipment', [EquipmentController::class, 'store'])->name('equipment.store');
    });

    Route::middleware('permission:equipment.update')->group(function () {
        Route::get('equipment/{equipment}/edit', [EquipmentController::class, 'edit'])->name('equipment.edit');
        Route::put('equipment/{equipment}', [EquipmentController::class, 'update'])->name('equipment.update');
        Route::patch('equipment/{equipment}', [EquipmentController::class, 'update']);
    });

    Route::middleware('permission:equipment.delete')->group(function () {
        Route::get('equipment/{equipment}/delete', [EquipmentController::class, 'confirmDelete'])->name('equipment.delete');
        Route::delete('equipment/{equipment}', [EquipmentController::class, 'destroy'])->name('equipment.destroy');
    });
});
