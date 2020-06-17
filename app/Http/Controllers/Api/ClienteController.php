<?php

namespace App\Http\Controllers\Api;

use App\tb_clientes;
use App\API\ApiError;
use App\Http\Controllers\Api\ValidacaoGenericaController;
use Illuminate\Http\Request;

class ClienteController extends ValidacaoGenericaController
{

    private $cliente; 

    public function __construct(tb_clientes $cliente)
    {
        $this->cliente = $cliente;
    }

    public function index()
    {
        return response()->json($this->cliente->paginate(3));
    }


    public function cadastrar(Request $request)
    {
        try {

            $clienteData = new tb_clientes;

            if(!empty($request)){
                
                $mensagensError = array();

                $mensagensError[] = $mensagemError = parent::validarNome($request->nome); 

                $mensagensError[] = $mensagemError = self::validarPreenchimento($request->cpfCnpj, 'CPF/CNPJ');

                if(is_null($mensagemError)){

                    if(strlen(filter_var($request->cpfCnpj, FILTER_SANITIZE_NUMBER_INT)) < 11 ){
                
                        $mensagensError[] = $mensagemError = ['CPF/CNPJ inválido.'];

                    }else{

                        $cpf_cnpj = str_replace(array('.','-','/'), "", $request->cpfCnpj);
                        $count = strlen($cpf_cnpj);

                        if($count <= 10){

                            $mensagensError[] = $mensagemError = ['CPF/CNPJ Obrigatório.'];
                        }

                        if($count == 11){

                            $cpf = str_pad(preg_replace('[^0-9]', '', $cpf_cnpj), 11, '0', STR_PAD_LEFT);
                        
                            $mensagensError[] = $mensagemError = parent::validarCPF($cpf);
                            
                        }
                        else
                        {
                            $cnpj = str_pad(str_replace(array('.','-','/'),'',$cpf_cnpj),14,'0',STR_PAD_LEFT);
                            $mensagensError[] = $mensagemError = parent::validarCNPJ($cnpj);
                        }
                    }
                }
                $data = date('d/m/Y', strtotime($request->dataNascimento));
                
                $mensagensError[] = $mensagemError = parent::validarData($data);
                
                $mensagensError[] = $mensagemError = parent::validarEndereco($request->endereco);


                $countContato = strlen($request->contato);

                if($countContato == 11){
                    $ddd = substr($request->contato, -0, 2);
                    $digito = substr($request->contato, 2, 1);
                    $contato = substr($request->contato, 3);
                    $request->contato = '('.$ddd.')'.$digito.'.'.$contato;
                }
                
                $mensagensError[] = $mensagemError = parent::validarTelefone($request->contato);

                $mensagensError[] = $mensagemError = parent::validarEmail($request->email);

                $mensagensError[] = $mensagemError = parent::validarSenha($request->senha);

                $mensagensError[] = $mensagemError = parent::validarUF($request->uf);

                $mensagensError[] = $mensagemError = self::validarPreenchimento($request->cidade, 'Cidade');

                $mensagensError[] = $mensagemError = self::validarPreenchimento($request->bairro, 'Bairro');

                foreach ($mensagensError as $mensagem) {

                    if(!is_null($mensagem)){
                        $return = ['cod' => '300',
                                    'msg' => $mensagem]; 
                        return response()->json($return);
                    }
                    
                }
                    
                $senha = md5($request->senha);

                switch ($request->genero) {
                    case 1:
                        $genero = 'Masculino';
                        break;
                    case 2:
                        $genero = 'Feminino';
                        break;
                    default:
                        $genero = null;
                        break;
                 }

                $clienteData = array(
                    'nome'=> $request->nome,
                    'genero' =>$genero,
                    'data_nascimento' => date('Y-m-d', strtotime($request->dataNascimento)),
                    'cpf_cnpj' =>$cpf_cnpj,
                    'endereco' =>$request->endereco,
                    'cidade' =>$request->cidade,
                    'bairro' =>$request->bairro,
                    'uf' =>$request->uf,
                    'telefone' =>$request->contato,
                    'email' =>$request->email,
                    'senha' =>$senha
                );
                
                session_start();
                include('Conexao.php');
                $query = "select cpf_cnpj from tb_clientes where cpf_cnpj = '{$cpf_cnpj}'";
                $result = mysqli_query($conexao, $query);
                $row = mysqli_num_rows($result);

                if($row > 0){
                    //Cliente já cadastrado. 
                    $return = ['cod' => 400];
                    return response()->json($return);
                }

                $this->cliente->create($clienteData);   
                
                $return = ['cod' => '200',
                                    'msg' => 'Cadastro realizado com sucesso!']; 

            }else{

                //Informações em falta
                $return = ['cod' => '300',
                        'msg' => 'Preencher os campos obrigatórios.'];             
            }

            return response()->json($return);
            

		} catch (\Exception $e) {

			if(config('app.debug')) {
				return response()->json(ApiError::errorMensagem($e->getMessage(), 1010), 500);
			}
			return response()->json(ApiError::errorMensagem('Houve um erro ao finalizar o cadastro', 1010),  500);
        }
        
    }
}
