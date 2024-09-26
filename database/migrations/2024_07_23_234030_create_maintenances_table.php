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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('product', 100);
            $table->text('description');
            $table->string('reference', 100)->nullable();
            $table->decimal('price', 8, 2);
            $table->date('delivery_date')->nullable();
            $table->string('image1', 100);
            $table->string('image2', 100)->nullable();
            $table->string('image3', 100)->nullable();
            $table->string('image4', 100)->nullable();
            $table->enum('advance', ['joined','in_progress','authorization','finalized'])->default('joined');
            $table->enum('repaired', ['true','false'])->default('false');
            $table->enum('warranty', ['true','false'])->default('false');
            $table->timestamps();
            $table->foreignId('users_id')->constrained('users')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
