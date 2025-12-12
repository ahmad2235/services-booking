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
        Schema::create('provider_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            // Business / public facing fields
            $table->string('company_name')->nullable();
            $table->string('title')->nullable(); // e.g., "Plumber", "Electrician"
            $table->string('phone')->nullable();
            $table->text('bio')->nullable();
            // Years of experience (kept singular consistent field across app)
            $table->integer('years_of_experience')->nullable();
            $table->decimal('min_price', 10, 2)->nullable();
            $table->decimal('max_price', 10, 2)->nullable();
            $table->text('coverage_description')->nullable();
            $table->decimal('avg_rating', 3, 2)->default(0.00);
            $table->integer('total_reviews')->default(0);
            $table->timestamps();

            // Indexes
            $table->index('avg_rating');
            $table->index(['min_price', 'max_price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_profiles');
    }
};
