<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->integer('edad');
            $table->string('celular');
            $table->string('correo')->nullable();
            $table->string('plan');
            $table->string('tipo_pago');
            $table->decimal('cantidad', 10, 2)->nullable();
            $table->string('baucher')->nullable();
            $table->string('foto')->nullable(); // para guardar ruta de la foto
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
}
