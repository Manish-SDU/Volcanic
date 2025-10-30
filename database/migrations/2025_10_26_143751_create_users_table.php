<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('surname', 100)->nullable();
            $table->string('username', 60)->unique();
            $table->date('date_of_birth')->nullable();
            $table->string('where_from', 100)->nullable(); 
            $table->text('bio')->nullable();
            $table->string('password'); 
            $table->boolean('is_admin')->default(false); 

            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
