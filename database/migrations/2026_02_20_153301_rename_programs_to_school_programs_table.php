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
        if (Schema::hasTable('programs') && !Schema::hasTable('school_programs')) {
            Schema::rename('programs', 'school_programs');
        }

        // Rename column if needed
        if (Schema::hasTable('school_programs') && Schema::hasColumn('school_programs', 'institution_id')) {
            Schema::table('school_programs', function (Blueprint $table) {
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
        if (Schema::hasTable('school_programs') && Schema::hasColumn('school_programs', 'school_id')) {
            Schema::table('school_programs', function (Blueprint $table) {
                $table->renameColumn('school_id', 'institution_id');
            });
        }

        // Rename table back
        if (Schema::hasTable('school_programs') && !Schema::hasTable('programs')) {
            Schema::rename('school_programs', 'programs');
        }
    }
};
