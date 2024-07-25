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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nit')->unique()->nullable();
            $table->string('phone1')->unique();
            $table->string('phone2');
            $table->string('address');
            $table->string('email')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->foreignId('cities_id')->constrained('cities')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
