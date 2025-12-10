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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->onDelete('cascade');
            $table->foreignId('provider_profile_id')->constrained('provider_profiles')->onDelete('cascade');
            $table->tinyInteger('rating_value')->unsigned(); // 1-5
            $table->text('comment')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->foreignId('hidden_by_admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('hidden_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('provider_profile_id');
            $table->index('is_visible');
            $table->index('rating_value');
            $table->index(['provider_profile_id', 'is_visible']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
