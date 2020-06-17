<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTbFornecedores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_fornecedores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome_fantasia');
			$table->string('nome_responsavel');
            $table->string('cpf_cnpj')->unique();
			$table->string('inscricao_estadual')->nullable();
            $table->string('endereco');
            $table->string('cidade');
            $table->string('bairro');
			$table->string('uf');
            $table->string('telefone');
			$table->string('telefone_adicional')->nullable();
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
        Schema::dropIfExists('tb_fornecedores');
    }
}
