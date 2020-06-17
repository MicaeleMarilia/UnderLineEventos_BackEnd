<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_servico_fornecedor extends Model
{
    protected $fillable = [
        'tipo_servico',
        'fornecedor',
        'nome',
        'capacidade_max',
        'preco',
        'qtd_horas_min',
        'qtd_horas_max',
        'descricao',
        'destaque',
    ];

    protected $table = 'tb_servico_fornecedor';
}
