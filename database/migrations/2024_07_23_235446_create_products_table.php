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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->unique();
            $table->string('slug', 255)->unique();
            $table->string('reference', 100)->unique();
            $table->string('barcode',100)->nullable()->unique();
            $table->text('description');
            $table->decimal('stock', 6, 2)->nullable();
            $table->decimal('sale_price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->enum('visible', ['true', 'false'])->default('true');
            $table->string('image1', 255);
            $table->string('image2', 255)->nullable();
            $table->string('image3', 255)->nullable();
            $table->string('image4', 255)->nullable();
            $table->string('image5', 255)->nullable();
            $table->string('color', 50)->nullable();
            $table->timestamps();
            $table->foreignId('categories_products_id')->constrained('categories_products')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
