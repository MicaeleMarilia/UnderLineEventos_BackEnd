<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tb_tipo_servico extends Model
{
    protected $fillable = [
        'nome',
    ];

    protected $table = 'tb_tipo_servico';
}
