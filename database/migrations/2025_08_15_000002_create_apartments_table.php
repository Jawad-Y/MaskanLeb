<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('judiciary_id')->constrained('judiciaries')->restrictOnDelete();

            $table->string('title', 255);
            $table->text('description');
            $table->decimal('price_usd', 10, 2);

            $table->unsignedInteger('number_of_rooms');
            $table->unsignedInteger('number_of_bathrooms');
            $table->unsignedInteger('size_m2');

            $table->boolean('furnished')->default(false);
            $table->boolean('parking')->default(false);

            $table->unsignedInteger('minimum_months')->default(1);

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->enum('status', ['available', 'rented', 'pending'])->default('available');
            $table->boolean('is_verified')->default(false);

            $table->unsignedInteger('views_count')->default(0);

            $table->timestamps();

            // Performance indexes
            $table->index('price_usd');
            $table->index('number_of_rooms');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
