<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\API\ApiError;


class LoginController extends Controller
{
 
    public function verificaarLongin(){

        if(!$_SESSION['usuario']) {
            $return = ['msg' => 'Informe o Usúario e Senha.'];            
            return response()->json($return, 100);
        }
    }

    public function login(Request $request){

        session_start();
        include('conexao.php');

        if(is_null($request->login) || is_null($request->senha)) {;
            $return = ['cod' => 300, 'msg' => 'Informe o Usúario e Senha.'];          
            return response()->json($return);
        }

        $usuario = mysqli_real_escape_string($conexao, $request->login);
        $senha = mysqli_real_escape_string($conexao, $request->senha);
        
        $queryCliente = "select id from tb_clientes where email = '{$usuario}' and senha = md5('{$senha}')";
        $resultCliente = mysqli_query($conexao, $queryCliente);
        $rowCliente = mysqli_fetch_array($resultCliente, MYSQLI_NUM);

        $queryFornecedor = "select id from tb_fornecedores where email = '{$usuario}' and senha = md5('{$senha}')";
        $resultFornecedor = mysqli_query($conexao, $queryFornecedor);
        $rowFornecedor = mysqli_fetch_array($resultFornecedor, MYSQLI_NUM);

        if(is_null($rowCliente)){
            $row = $rowFornecedor;
            $tipo = "F";
        }else{
            $row = $rowCliente;
            $tipo = "C";
        }

        if(!is_null($row)) {
            $_SESSION['usuario'] = $row[0];
            $user = base64_encode($row[0]);
            $return = ['cod' => 200, 'user' => $user, 'tipo' => $tipo];   
            return response()->json($return);
            exit();
        } else {
            $_SESSION['nao_autenticado'] = true;
            //'Usúario não cadastrado.'];
            $return = ['cod' => 300, 'msg' => 'Usúario ou Senha inválida.'];             
            return response()->json($return);
            exit();
        }
    }

    public function logout(){
        
        session_start();
        session_destroy();
        $return = ['msg' => 'Usúario Deslogado com sucesso.'];            
        return response()->json($return, 200);
        exit();
    }
}
