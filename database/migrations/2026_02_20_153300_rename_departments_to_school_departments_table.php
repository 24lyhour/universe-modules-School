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
        // Rename table if needed
        if (Schema::hasTable('departments') && !Schema::hasTable('school_departments')) {
            Schema::rename('departments', 'school_departments');
        }

        // Rename column if needed
        if (Schema::hasTable('school_departments') && Schema::hasColumn('school_departments', 'institution_id')) {
            Schema::table('school_departments', function (Blueprint $table) {
                $table->renameColumn('institution_id', 'school_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename column back
        if (Schema::hasTable('school_departments') && Schema::hasColumn('school_departments', 'school_id')) {
            Schema::table('school_departments', function (Blueprint $table) {
                $table->renameColumn('school_id', 'institution_id');
            });
        }

        // Rename table back
        if (Schema::hasTable('school_departments') && !Schema::hasTable('departments')) {
            Schema::rename('school_departments', 'departments');
        }
    }
};
