<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description');
            $table->enum('metric', ['total_visits', 'visits_by_continent', 'visits_by_activity', 'visits_by_type']);
            $table->json('dimensions')->nullable();
            $table->enum('aggregator', ['count', 'count_distinct']);
            $table->integer('threshold')->default(1);
            $table->string('image_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
