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
        Schema::create('shooping_cart', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2)->required();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shooping_cart');
    }
};
