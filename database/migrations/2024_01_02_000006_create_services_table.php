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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('service_categories')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            // keep legacy seed/view friendly fields
            $table->decimal('base_price', 10, 2)->nullable();
            $table->integer('duration_minutes')->nullable();

            // backward compatible fields (older code paths)
            $table->integer('default_duration_minutes')->nullable();
            $table->decimal('default_price_from', 10, 2)->nullable();
            $table->decimal('default_price_to', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('category_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
