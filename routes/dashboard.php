<?php

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\Dashboard\V1\SchoolController;

Route::middleware(['auth', 'verified'])->prefix('dashboard')->group(function () {
    Route::resource('schools', SchoolController::class)->names('schools');
    Route::get('schools/{school}/delete', [SchoolController::class, 'confirmDelete'])->name('schools.delete');
    Route::put('schools/{school}/toggle-status', [SchoolController::class, 'toggleStatus'])->name('schools.toggle-status');
});
