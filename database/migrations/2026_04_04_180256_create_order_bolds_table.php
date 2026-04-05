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
        Schema::create('order_bolds', function (Blueprint $table) {
            $table->id();
 // 🔑 Identificador único de la orden (tu sistema)
            $table->string('order_id')->unique();

            // 💰 Información del pago
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3); // COP, USD, etc.

            // 🔄 Estado del pago
            $table->string('status')->default('pending');

            // 🔗 Referencia que devuelve Bold
            $table->string('reference')->nullable();

            // 🧾 (Opcional pero PRO 🔥)
            $table->json('bold_response')->nullable();
            //referencia para guardar si las firmas coinciden o no
            $table->string('signature_valid')->default('false');

            // ⏰ Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_bolds');
    }
};
