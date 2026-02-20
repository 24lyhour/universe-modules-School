<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('courses') && !Schema::hasTable('school_courses')) {
            Schema::rename('courses', 'school_courses');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('school_courses') && !Schema::hasTable('courses')) {
            Schema::rename('school_courses', 'courses');
        }
    }
};
