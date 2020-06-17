<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTbClientes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_clientes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome');
            $table->string('genero')->nullable();
            $table->date('data_nascimento');
            $table->string('cpf_cnpj')->unique();
            $table->string('endereco');
            $table->string('cidade');
            $table->string('bairro');
			$table->string('uf');
            $table->string('telefone');
            $table->string('email');
            $table->string('senha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tb_clientes');
    }
}
