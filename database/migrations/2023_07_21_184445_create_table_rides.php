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
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->boolean('public')->default(false);
            $table->integer('distance')->nullable();
            $table->integer('duration')->nullable();
            $table->integer('max_speed')->nullable();
            $table->integer('avg_speed')->nullable();
            $table->json('positions')->nullable();

            // Foreign key
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
