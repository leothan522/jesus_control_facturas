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
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero');
            $table->date('fecha');
            $table->string('moneda')->nullable();
            $table->boolean('es_credito')->boolean(false);
            $table->integer('dias_credito')->nullable();
            $table->bigInteger('empresas_id')->unsigned();
            $table->string('empresa_rif')->nullable();
            $table->string('empresa_nombre')->nullable();
            $table->string('empresa_telefono')->nullable();
            $table->string('empresa_email')->nullable();
            $table->text('empresa_direccion')->nullable();
            $table->text('empresa_image')->nullable();
            $table->bigInteger('clientes_id')->unsigned();
            $table->string('cliente_rif')->nullable();
            $table->string('cliente_nombre')->nullable();
            $table->string('cliente_telefono')->nullable();
            $table->string('cliente_email')->nullable();
            $table->text('cliente_direccion')->nullable();
            $table->decimal('sub_total', 12, 2)->nullable();
            $table->decimal('total', 12, 2)->nullable();
            $table->boolean('estatus')->default(false);
            $table->foreign('empresas_id')->references('id')->on('empresas')->cascadeOnDelete();
            $table->foreign('clientes_id')->references('id')->on('clientes')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
