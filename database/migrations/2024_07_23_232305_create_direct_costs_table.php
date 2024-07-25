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
        Schema::create('direct_costs', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->text('description');
            $table->decimal('amount', 8, 2);
            $table->decimal('price', 8, 2);
            $table->timestamps();
            $table->foreignId('categories_direct_costs_id')->constrained('categories_direct_costs')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direct_costs');
    }
};
