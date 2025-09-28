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
            $table->foreignId('sales_id')->nullable()->constrained('sales')->onDelete('cascade')->onUpdate('cascade');;
            $table->foreignId('indirect_costs_id')->nullable()->constrained('indirect_costs')->onDelete('cascade')->onUpdate('cascade');;
            $table->foreignId('direct_costs_id')->nullable()->constrained('direct_costs')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('purchase_orders_id')->nullable()->constrained('purchaclearse_orders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('maintenances_id')->nullable()->constrained('maintenances')->onDelete('cascade')->onUpdate('cascade');
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
