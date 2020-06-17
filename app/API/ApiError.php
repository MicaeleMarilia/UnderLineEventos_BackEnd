<?php

namespace App\API;

class ApiError
{
    public static function errorMensagem($mensagem, $cod)
    {
        return[
            'data' => [
                'msg' => $mensagem,
                'cod' => $cod
            ]  
        ];
    }
}