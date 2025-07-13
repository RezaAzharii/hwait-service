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
        Schema::create('target', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('title', 100);
            $table->decimal('ticket', 12, 2)->default(0);
            $table->decimal('food', 12, 2)->default(0);
            $table->decimal('transport', 12, 2)->default(0);
            $table->decimal('others', 12, 2)->default(0);
            $table->string('image_path');
            $table->string('location_name', 150);
            $table->decimal('latitude');
            $table->decimal('longitude');
            $table->enum('status', ['pending', 'selesai'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target');
    }
};