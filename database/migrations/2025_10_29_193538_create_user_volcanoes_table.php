<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_volcanoes', function (Blueprint $table) {
            $table->id('list_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('volcanoes_id')->constrained('volcanoes')->onDelete('cascade');
            $table->text('note')->nullable();
            $table->enum('status', ['visited', 'wishlist']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_volcanoes');
    }
};

// should I add the created_at and updated_at timestamps?
