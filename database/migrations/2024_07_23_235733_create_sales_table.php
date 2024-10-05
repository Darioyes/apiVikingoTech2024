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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->required();
            $table->enum('confirm_sale', ['true', 'false'])->default('false')->required();
            $table->enum('shopping_cart', ['true', 'false'])->default('false')->required();
            $table->decimal('sale_total', 10, 2)->required();
            $table->decimal('cost_total', 10, 2)->required();
            $table->timestamps();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
