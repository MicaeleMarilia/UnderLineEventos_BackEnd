<?php

namespace App\Http\Controllers\Api;

use App\tb_servico_fornecedor;
use App\API\ApiError;
use App\Http\Controllers\Api\ValidacaoGenericaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicoFornecedorController extends ValidacaoGenericaController
{
    private $servicoFornecedor; 

    public function __construct(tb_servico_fornecedor $servicoFornecedor)
    {
        $this->servicoFornecedor = $servicoFornecedor;
    }

    public function listar()
    {
        try{

            return response()->json($this->servicoFornecedor->paginate(5));
            
        } catch (\Exception $e) {
            if(config('app.debug')) {
                return response()->json(ApiError::errorMensagem($e->getMessage(), 1011),  500);
            }
            return response()->json(ApiError::errorMensagem('Houve um erro ao realizar operação de atualizar', 1011), 500);
        }
    }

    public function listarDestaque()
    {
        try{

            $query = DB::table('tb_servico_fornecedor')
            ->join('tb_fornecedores', 'tb_servico_fornecedor.fornecedor', '=', 'tb_fornecedores.id')
            ->join('tb_tipo_servico', 'tb_servico_fornecedor.tipo_servico', '=', 'tb_tipo_servico.id')
            ->where('tb_servico_fornecedor.destaque', '=', '1')
            ->orderByRaw("RAND()")
            ->limit(3);

            $result = $query->get();

			return response()->json($result);
            
        } catch (\Exception $e) {
            if(config('app.debug')) {
                return response()->json(ApiError::errorMensagem($e->getMessage(), 1011),  500);
            }
            return response()->json(ApiError::errorMensagem('Houve um erro ao realizar operação de atualizar', 1011), 500);
        }
    }

    public function cadastrar(Request $request)
    {
        try {
            
            $user = base64_decode($request->user);
            //echo (json_encode($user));
            if(!empty($_FILES)){
                
                $fornecedor = base64_decode($_POST['idForn']);
                $servico = $_POST['idServ'];

                $caminho = '/xampp/htdocs/Servidor/';
                $caminhoCompleto = '/xampp/htdocs/Servidor/'.$fornecedor.'/'.$servico;

                if (!empty($_FILES)){
                    if(!file_exists($caminho))
                        // mkdir('/xampp/htdocs/Servidor', 0777, true);
                        mkdir($caminho, 0777, true);
                    if(!file_exists($caminho.$fornecedor))
                        mkdir($caminho.$fornecedor);
                    if(!file_exists($caminho.$fornecedor.'/'.$servico))
                        mkdir($caminho.$fornecedor.'/'.$servico);
                    echo( count($_FILES)); 
                    for($i=0 ; $i < count($_FILES) ; $i++ ){
                        move_uploaded_file($_FILES['fotos'.$i]['tmp_name'], $caminhoCompleto.'/'.date('YmdHms').$i.'.jpg');
                        
                    }
                }
                //echo (json_encode($_FILES['fotos0']));
                $imagens = array();

                if(is_dir($caminhoCompleto)){
                    chdir($caminhoCompleto);

                    $caminho_retorno = 'http://localhost:81/Servidor/'.$fornecedor.'/'.$servico;

                    $arquivos = glob("{*.png,*.jpg,*.jpeg,*.bmp,*.gif}", GLOB_BRACE);
                    for($i = 0 ; $i <= count($arquivos) ; $i++) {
                        if (!empty($arquivos[$i]) && $arquivos[$i] != '.' && $arquivos[$i] != '..')
                            $imagens[] = $caminho_retorno.'/'.$arquivos[$i];
                    }
                }
            }else{

                $servicoFornecedorData = new tb_servico_fornecedor;
                if(!empty($request)){
                    
                    $mensagensError = array();

                    $mensagensError[] = parent::validarNome($request->nomeServico); 

                    $mensagensError[] = parent::validarPreenchimento($request->opServico, 'Tipo de Serviço');

                    if(filter_var($request->capacidade, FILTER_SANITIZE_NUMBER_INT) == ''){
                
                        $mensagensError[] = ['Preencha a Capacidade Máxima com números.'];
                    }

                    if(is_double($request->valor) || is_null($request->valor)){
                
                        $mensagensError[] = ['Preencha o Preço com valores válidos.'];
                    }


                    $mensagensError[] = parent::validarHM($request->qtdHrsMin);
                    $mensagensError[] = parent::validarHM($request->qtdHrsMax);

                    $mensagensError[] = parent::validarPreenchimento($request->descricao, 'Descrição');               

                    foreach ($mensagensError as $mensagem) {

                        if(!is_null($mensagem)){
                            $return = ['cod' => '300',
                                        'msg' => $mensagem]; 
                            return response()->json($return);
                        }
                        
                    }

                    session_start();
                    include('Conexao.php');
                    $query = "select id from tb_servico_fornecedor where fornecedor = '{$user}'
                                and tipo_servico = {$request->opServico} and nome = '{$request->nomeServico}'";
                    
                    $result = mysqli_query($conexao, $query);
                    $row = mysqli_num_rows($result);
                    //die(var_dump($result));
                    if($row > 0){
                        //Servico já cadastrado. 
                        $return = ['cod' => 400];
                        return response()->json($return);
                    }

                    $servicoFornecedorData = array(
                        'tipo_servico' => $request->opServico,
                        'fornecedor' => $user,
                        'nome' => $request->nomeServico, 
                        'capacidade_max' => $request->capacidade,
                        'preco' => $request->valor,
                        'qtd_horas_min' => $request->qtdHrsMin,
                        'qtd_horas_max' => $request->qtdHrsMax,
                        'descricao' => $request->descricao,
                        //'imagem1' => $conteudo
                    );
                    //die(var_dump($servicoFornecedorData));
                    $new = $this->servicoFornecedor->create($servicoFornecedorData);
                    $return = ['cod' => 200,
                                'idServ' => $new->id];
                }else{

                    //Informações em falta
                    $return = ['cod' => 'Preencher os campos obrigatórios.'];               
                }

                return response()->json($return);
            }    

		} catch (\Exception $e) {

			if(config('app.debug')) {
				return response()->json(ApiError::errorMensagem($e->getMessage(), 1010), 500);
			}
			return response()->json(ApiError::errorMensagem('Houve um erro ao finalizar o cadastro', 1010),  500);
        }
        
    }

    public function pesquisar(Request $request)
	{
        try {

            $mensagensError = parent::validarNome($request->nomeServico); 

            if(!is_null($mensagensError)){
                $return = ['cod' => 300,
                            'msg' => $mensagensError];
                    return response()->json($return);
            }
            session_start();
            include('Conexao.php');
            $query = "select id from tb_servico_fornecedor where nome like '%{$request->nomeServico}%'";
            $result = mysqli_query($conexao, $query);
            $row = mysqli_num_rows($result);

            if($row == 0){
                //Servico não encontrado.. 
                $return = ['cod' => 300,
                            'msg' => 'Serviço não encontrado.'];
                    return response()->json($return);
            }else{
                $id = $result->fetch_object()->id;
                $servicoFornecedor     = $this->servicoFornecedor->find($id);
                return response()->json($servicoFornecedor);
            }
        } catch (\Exception $e) {
			if(config('app.debug')) {
				return response()->json(ApiError::errorMensagem($e->getMessage(), 1011),  500);
			}
			return response()->json(ApiError::errorMensagem('Houve um erro ao realizar operação de atualizar', 1011), 500);
		}
    }

    public function atualizar(Request $request, $id)
	{
		try {

			$servicoFornecedorData = $request->all();
			$servicoFornecedor     = $this->servicoFornecedor->find($id);
			$servicoFornecedor->update($servicoFornecedorData);

            $return = ['cod' => '200',
                                    'msg' => 'Serviço atualizado com sucesso!'];
			return response()->json($return);

		} catch (\Exception $e) {
			if(config('app.debug')) {
				return response()->json(ApiError::errorMensagem($e->getMessage(), 1011),  500);
			}
			return response()->json(ApiError::errorMensagem('Houve um erro ao realizar operação de atualizar', 1011), 500);
		}
    }
    
    public function delete(tb_servico_fornecedor $id)
	{
		try {
            
            //die(var_dump($id));
			$id->delete();

			return response()->json(['data' => ['msg' => 'Serviço: ' . $id->name . ' removido com sucesso!']], 200);

		}catch (\Exception $e) {
			if(config('app.debug')) {
				return response()->json(ApiError::errorMensagem($e->getMessage(), 1012),  500);
			}
			return response()->json(ApiError::errorMensagem('Houve um erro ao realizar operação de remover', 1012),  500);
		}
    }
    
    public function pesquisaPrincipal(Request $request)
	{
		try {
           //echo($request->cidade);
            $query = DB::table('tb_servico_fornecedor')
                    ->join('tb_fornecedores', 'tb_servico_fornecedor.fornecedor', '=', 'tb_fornecedores.id')
                    ->join('tb_tipo_servico', 'tb_servico_fornecedor.tipo_servico', '=', 'tb_tipo_servico.id');
                    if((!($request->tipoServico == 0)) && !is_null($request->tipoServico)){
                        
                        $query->where('tb_servico_fornecedor.tipo_servico', '=', $request->tipoServico);
                    }
                    if(!($request->cidade == 0) && !is_null($request->cidade)){

                        $query->where('tb_fornecedores.cidade', '=', $request->cidade);
                    }
                    if(!($request->bairro == 0) && !is_null($request->bairro)){
 
                        $query->where('tb_fornecedores.bairro', '=', $request->bairro);
                    }
                    $query->orderBy('tb_servico_fornecedor.nome', 'asc');
                    
            $result = $query->get();
            $count = $query->sum('tb_servico_fornecedor.id');

            if($count == 0){

                $return = ['cod' => '300',
                                        'msg' => 'Nenhum resultado encontrado.']; 
                            return response()->json($return);
            }else{

                return response()->json($result);
            }
            

		} catch (\Exception $e) {
			if(config('app.debug')) {
				return response()->json(ApiError::errorMensagem($e->getMessage(), 1011),  500);
			}
			return response()->json(ApiError::errorMensagem('Houve um erro ao realizar operação.', 1011), 500);
		}
	}
}
