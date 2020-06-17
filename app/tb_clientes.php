<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_clientes extends Model
{
    protected $fillable = [
        'nome',
        'data_nascimento',
        'genero',
        'cpf_cnpj',
        'endereco',
        'cidade',
        'bairro',
        'uf',
        'telefone',
        'email',
        'senha',
    ];

    protected $table = 'tb_clientes';
}
