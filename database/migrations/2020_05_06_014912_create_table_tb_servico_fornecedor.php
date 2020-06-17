<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTbServicoFornecedor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tb_servico_fornecedor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('tipo_servico')->unsigned();
            $table->foreign('tipo_servico')->references('id')->on('tb_tipo_servico');
            $table->unsignedBigInteger('fornecedor')->unsigned();
            $table->foreign('fornecedor')->references('id')->on('tb_fornecedores');
            $table->string('nome');
            $table->string('capacidade_max');
            $table->double('preco', 8, 2);
            $table->time('qtd_horas_min', 0);
            $table->time('qtd_horas_max', 0);
            $table->string('descricao', 1000);
            $table->boolean('destaque')->nullable();
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
        Schema::dropIfExists('tb_servico_fornecedor');
    }
}
