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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_profile_id')->constrained('provider_profiles')->onDelete('cascade');
            $table->foreignId('provider_service_id')->constrained('provider_services')->onDelete('cascade');
            $table->foreignId('time_slot_id')->nullable()->constrained('provider_time_slots')->onDelete('set null');
            // Standard name used throughout the app
            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->string('address')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'cancelled', 'completed'])->default('pending');
            // general notes used by seeders/tests
            $table->text('notes')->nullable();
            $table->text('customer_note')->nullable();
            $table->text('provider_note')->nullable();
            $table->text('reject_reason')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->timestamps();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            // Indexes
            $table->index('customer_id');
            $table->index('provider_profile_id');
            $table->index('provider_service_id');
            $table->index('time_slot_id');
            $table->index('status');
            $table->index('scheduled_at');
            $table->index(['provider_profile_id', 'status']);
            $table->index(['customer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
