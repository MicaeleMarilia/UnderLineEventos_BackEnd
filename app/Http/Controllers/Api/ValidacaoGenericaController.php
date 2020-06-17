<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ValidacaoGenericaController extends Controller
{
    public function  __construct() {

    }

    public static function validarPreenchimento(String $valor = null, String $campo = null)
    {
        if(empty($valor) || $valor == null)
        {
            $return = $campo.' Obrigatório.';            
            return $return;
            
        }
    }

    public static function validarNome(String $valor = null)
    { 

        $mensagemErro = self::validarPreenchimento($valor, 'Nome');
        
        if(!is_null($mensagemErro)){
            return $mensagemErro;
        }

        if(strlen($valor)<=2){
            
            $return = 'Preencha o Nome com no mínimo 2 caracteres.';
            return $return;
        }
    }

    public function validarData(String $valor = null)
    {

        if($valor == null)
        {

            $return = 'Data Obrigatória.';            
            return $return;
            
        }
        
        if(!preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $valor) && $valor <> null) {
            $return = 'Data inválida.';
            return $return;
        }
    }

    public static function validarEndereco(String $valor = null)
    { 
        $mensagemErro = self::validarPreenchimento($valor, 'Endereço');
        
        if(!is_null($mensagemErro)){
            return $mensagemErro;
        }

        if(strlen($valor)<=4){
            
            $return = 'Preencha um Endereço válido.';
            return $return;
        }
    }

    public function validarTelefone(String $valor = null)
    {
        $mensagemErro = self::validarPreenchimento($valor, 'Telefone');
        
        if(!is_null($mensagemErro)){
            return $mensagemErro;
        }

        $regex = '/^(?:(?:\+|00)?(55)\s?)?(?:\(?([1-9][0-9])\)?\s?)?(?:((?:9\d|[2-9])\d{3})\-?(\d{4}))$/';
        
        if(preg_match($regex, $valor)){
            $return = 'Telefone inválido.';
            return $return;
        }
    }

    public function validarEmail(String $valor = null)
    {
        $mensagemErro = self::validarPreenchimento($valor, 'Email');
        
        if(!is_null($mensagemErro)){
            return $mensagemErro;
        }
        
        if(!filter_var($valor, FILTER_VALIDATE_EMAIL)){
            $return = 'Email inválido.';
            return $return;
        }
    }

    public function validarSenha(String $valor1 = null)
    {
        $mensagemErro = self::validarPreenchimento($valor1, 'Senha');
        
        if(!is_null($mensagemErro)){
            return $mensagemErro;
        }

        if(strlen($valor1)<=7){

            $return = 'Preencha a Senha com no mínimo 8 caracteres incluindo letras e números.';
            return $return;
        }

        if(filter_var($valor1, FILTER_SANITIZE_NUMBER_INT) == ''){
            
            $return = 'Preencha a Senha com números e letras.';
            return $return;
        }
    }

    public function validarCPF(String $cpf = null)
    {
        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || 
            $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || 
            $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || 
            $cpf == '88888888888' || $cpf == '99999999999'):
            $return = 'CPF inválido';
            return $return;
        else: 
            for ($t = 9; $t < 11; $t++):
                for ($d = 0, $c = 0; $c < $t; $c++) :
                    $d += $cpf[$c] * (($t + 1) - $c);
                endfor;
                    $d = ((10 * $d) % 11) % 10;
                    if ($cpf[$c] != $d):
                        $return = 'CPF inválido.';
                        return $return;
                    endif;
                endfor;
            endif;
    }

    public function validarCNPJ(String $cnpj = null)
    {
        if (strlen($cnpj) != 14):
            $return = 'CNPJ inválido';
            return $return;
        else:
            for($t = 12; $t < 14; $t++):
                for($d = 0, $p = $t - 7, $c = 0; $c < $t; $c++):
                    $d += $cnpj[$c] * $p;
                    $p  = ($p < 3) ? 9 : --$p;
                endfor;
                $d = ((10 * $d) % 11) % 10;
                if($cnpj[$c] != $d):
                    $return = 'CNPJ inválido.';
                    return $return;
                endif;
            endfor;
        endif;
    }

    public function validarUF(String $uf = null){

        $estadosBrasileiros = array(
            'AC'=>'Acre',
            'AL'=>'Alagoas',
            'AP'=>'Amapá',
            'AM'=>'Amazonas',
            'BA'=>'Bahia',
            'CE'=>'Ceará',
            'DF'=>'Distrito Federal',
            'ES'=>'Espírito Santo',
            'GO'=>'Goiás',
            'MA'=>'Maranhão',
            'MT'=>'Mato Grosso',
            'MS'=>'Mato Grosso do Sul',
            'MG'=>'Minas Gerais',
            'PA'=>'Pará',
            'PB'=>'Paraíba',
            'PR'=>'Paraná',
            'PE'=>'Pernambuco',
            'PI'=>'Piauí',
            'RJ'=>'Rio de Janeiro',
            'RN'=>'Rio Grande do Norte',
            'RS'=>'Rio Grande do Sul',
            'RO'=>'Rondônia',
            'RR'=>'Roraima',
            'SC'=>'Santa Catarina',
            'SP'=>'São Paulo',
            'SE'=>'Sergipe',
            'TO'=>'Tocantins'
            );

        $mensagemErro = self::validarPreenchimento($uf, 'UF');
        
        if(!is_null($mensagemErro)){
            return $mensagemErro;
        }

        $encontrado = false;

        foreach ($estadosBrasileiros as $estado) {
            if($estado == $uf){

                $encontrado = true;
            }
        }
            
        if(!$encontrado){

            $return = 'UF inválido.';
            return $return;
        }
    }

    public function validarHM(String $horas = null){

        $mensagemErro = self::validarPreenchimento($horas, 'Horas de serviço');
        
        if(!is_null($mensagemErro)){
            return $mensagemErro;
        }

        if (preg_match('/^[0-9]{2}:[0-9]{2}$/', $horas)) {
            $horas = substr($horas, 0, 2);
            $minutos = substr($horas, 3, 2);
            if (($horas > "23") OR ($minutos > "59")) {
               
                $return = 'Quantidade de Horas inválida.';
                return $return;
            }
        }
    }
}




