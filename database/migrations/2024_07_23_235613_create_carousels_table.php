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
        Schema::create('carousels', function (Blueprint $table) {
            $table->id();
            $table->enum('carousel', ['active', 'inactive'])->default('inactive');
            $table->integer('order')->unique();
            $table->string('image', 100);
            $table->string('image2', 100);
            $table->string('image3', 100)->nullable();
            $table->timestamps();
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carousels');
    }
};
