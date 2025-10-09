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

        // Values that we agreed in our Excel database
        Schema::create('volcanoes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country');
            $table->string('continent');
            $table->string('activity');
            $table->decimal('latitude', 10, 6);
            $table->decimal('longitude', 10, 6);
            $table->integer('elevation');
            $table->text('description');
            $table->string('type');
            $table->string('image_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volcanoes');
    }
};
