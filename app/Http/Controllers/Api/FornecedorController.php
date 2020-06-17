<?php

namespace App\Http\Controllers\Api;

use App\tb_fornecedores;
use App\API\ApiError;
use App\Http\Controllers\Api\ValidacaoGenericaController;
use Illuminate\Http\Request;

class FornecedorController extends ValidacaoGenericaController
{
    private $fornecedor; 

    public function __construct(tb_fornecedores $fornecedor)
    {
        $this->fornecedor = $fornecedor;
    }

    public function index()
    {
        return response()->json($this->fornecedor->paginate(3));
    }

    public function cadastrar(Request $request)
    {
        try {
            
            $fornecedorData = new tb_fornecedores;

            if(!empty($request)){
                
                $mensagensError = array();

                $mensagensError[] = $mensagemError = parent::validarNome($request->nomeFantasia);
                
                $mensagensError[] = $mensagemError = parent::validarNome($request->nomeResponsavel); 

                $mensagensError[] = $mensagemError = parent::validarEndereco($request->endereco);
                
                $mensagensError[] = $mensagemError = parent::validarUF($request->uf);

                $mensagensError[] = $mensagemError = self::validarPreenchimento($request->cidade, 'Cidade');

                $mensagensError[] = $mensagemError = self::validarPreenchimento($request->bairro, 'Bairro');

                $countContato = strlen($request->contato);
                if($countContato == 11){
                    $ddd = substr($request->contato, -0, 2);
                    $digito = substr($request->contato, 2, 1);
                    $contato = substr($request->contato, 3);
                    $request->contato = '('.$ddd.')'.$digito.'.'.$contato;
                }
                $mensagensError[] = $mensagemError = parent::validarTelefone($request->contato);

                if(!is_null($request->contato2)){
                $countContato2 = strlen($request->contato2);
                    if($countContato2 == 11){
                        $ddd = substr($request->contato2, -0, 2);
                        $digito = substr($request->contato2, 2, 1);
                        $contato = substr($request->contato2, 3);
                        $request->contato2 = '('.$ddd.')'.$digito.'.'.$contato;
                    }else{
                        $ddd = substr($request->contato2, -0, 2);
                        $contato = substr($request->contato2, 2);
                        $request->contato2 = '('.$ddd.')9.'.$contato;
                    }
                    
                    $mensagensError[] = $mensagemError = parent::validarTelefone($request->contato2);
                }
                
                $mensagensError[] = $mensagemError = parent::validarEmail($request->email);
                
                $mensagensError[] = $mensagemError = parent::validarSenha($request->senha);
                
                $mensagensError[] = $mensagemError = self::validarPreenchimento($request->cpfCnpj, 'CPF/CNPJ');

                if(is_null($mensagemError)){

                    if(strlen(filter_var($request->cpfCnpj, FILTER_SANITIZE_NUMBER_INT)) < 11 ){
                
                        $mensagensError[] = $mensagemError = 'CPF/CNPJ inválido.';

                    }else{

                        $cpf_cnpj = str_replace(array('.','-','/'), "", $request->cpfCnpj);
                        $count = strlen($cpf_cnpj);

                        if($count <= 10){

                            $mensagensError[] = $mensagemError = 'CPF/CNPJ Obrigatório.';
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

                foreach ($mensagensError as $mensagem) {

                    if(!is_null($mensagem)){

                        $return = ['cod' => '300',
                                    'msg' => $mensagem]; 
                        return response()->json($return);
                    }   
                }

                $senha = md5($request->senha);

                $fornecedorData = array(
                    'nome_fantasia' =>$request->nomeFantasia,
                    'nome_responsavel' =>$request->nomeResponsavel,
                    'cpf_cnpj' =>$cpf_cnpj,
                    'inscricao_estadual' =>$request->inscricaoEstadual,
                    'cidade' =>$request->cidade,
                    'bairro' =>$request->bairro,
                    'uf' =>$request->uf,
                    'endereco' =>$request->endereco,
                    'telefone' =>$request->contato,
                    'telefone_adicional' =>$request->contato2,
                    'email' =>$request->email,
                    'senha' =>$senha
                );

                session_start();
                include('Conexao.php');
                $query = "select cpf_cnpj from tb_fornecedores where cpf_cnpj = '{$cpf_cnpj}'";
                $result = mysqli_query($conexao, $query);
                $row = mysqli_num_rows($result);

                if($row > 0){
                    //Cliente já cadastrado. 
                    $return = ['cod' => 400];
                    return response()->json($return);
                }

                $this->fornecedor->create($fornecedorData);
                
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
