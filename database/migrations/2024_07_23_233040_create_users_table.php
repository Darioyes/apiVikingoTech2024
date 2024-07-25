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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45);
            $table->string('lastname',45);
            $table->string('email')->unique();
            $table->enum('gender', ['famele','male','other'])->default('other');
            $table->date('birthday')->nullable();
            $table->string('phone1', 12)->unique();
            $table->string('phone2', 12)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->foreignId('cities_id')->constrained('cities')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('vikingo_rol_id')->constrained('vikingo_rols')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
