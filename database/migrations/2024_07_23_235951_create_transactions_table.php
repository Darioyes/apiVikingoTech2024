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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('sales_id')->constrained('sales');
            $table->foreignId('indirect_costs_id')->constrained('indirect_costs');
            $table->foreignId('direct_costs_id')->constrained('direct_costs')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('purcharse_orders_id')->constrained('purcharse_orders')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
