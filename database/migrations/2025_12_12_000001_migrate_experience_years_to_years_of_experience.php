<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // If the legacy column exists, migrate data to the new column and drop the old one
        if (Schema::hasTable('provider_profiles') && Schema::hasColumn('provider_profiles', 'experience_years')) {
            // Ensure the new column exists
            if (!Schema::hasColumn('provider_profiles', 'years_of_experience')) {
                Schema::table('provider_profiles', function (Blueprint $table) {
                    $table->integer('years_of_experience')->nullable()->after('bio');
                });
            }

            // Copy data over
            DB::statement('UPDATE provider_profiles SET years_of_experience = experience_years WHERE years_of_experience IS NULL');

            // Drop the legacy column
            Schema::table('provider_profiles', function (Blueprint $table) {
                if (Schema::hasColumn('provider_profiles', 'experience_years')) {
                    $table->dropColumn('experience_years');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the legacy column and populate it back from the new column
        if (Schema::hasTable('provider_profiles') && !Schema::hasColumn('provider_profiles', 'experience_years')) {
            Schema::table('provider_profiles', function (Blueprint $table) {
                $table->integer('experience_years')->nullable()->after('bio');
            });

            DB::statement('UPDATE provider_profiles SET experience_years = years_of_experience WHERE experience_years IS NULL');

            // Optionally drop the new column (but we leave it to the down migration semantics)
            // Schema::table('provider_profiles', function (Blueprint $table) {
            //     $table->dropColumn('years_of_experience');
            // });
        }
    }
};
