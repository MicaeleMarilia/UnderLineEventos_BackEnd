<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_fornecedores extends Model
{
    protected $fillable = [
        'nome_fantasia',
        'nome_responsavel',
        'cpf_cnpj',
        'inscricao_estadual',
        'cidade',
        'bairro',
        'uf',
        'endereco',
        'telefone',
        'telefone_adicional',
        'email',
        'senha',
    ];

    protected $table = 'tb_fornecedores';
}
