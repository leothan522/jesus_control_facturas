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
        Schema::create('facturas_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('facturas_id')->unsigned();
            $table->bigInteger('articulos_id')->unsigned();
            $table->string('descripcion');
            $table->decimal('cantidad', 12,2);
            $table->string('moneda');
            $table->decimal('precio', 12,2);
            $table->foreign('facturas_id')->references('id')->on('facturas')->cascadeOnDelete();
            $table->foreign('articulos_id')->references('id')->on('articulos')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas_items');
    }
};
