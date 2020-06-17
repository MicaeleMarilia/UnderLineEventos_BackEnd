<?php

namespace App\Http\Controllers\Api;

use App\tb_tipo_servico;
use App\API\ApiError;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FiltroController extends Controller
{
    private $tipoServico;

	public function __construct(tb_tipo_servico $tipoServico)
	{
        $this->tipoServico = $tipoServico;
        
        if(!empty($this->tipoServico->all())){
            
            $this->salvar();
        }
	}

	public function listarTipoServico()
    {
        try{


            $tipoServico = DB::table('tb_tipo_servico')->distinct()->select('id', 'nome')->groupBy('id')->get();
            return response()->json($tipoServico);
        
        } catch (\Exception $e) {
            if(config('app.debug')) {
                return response()->json(ApiError::errorMensagem($e->getMessage(), 1011),  500);
            }
            return response()->json(ApiError::errorMensagem('Houve um erro ao realizar operação de atualizar', 1011), 500);
        }
    }

    //APAGAR
    public function show($id)
    {
    	$tipoServico = $this->tipoServico->find($id);

    	if(! $tipoServico) return response()->json(ApiError::errorMensagem('Produto não encontrado!', 4040), 404);

    	$data = ['data' => $tipoServico];
	    return response()->json($data);
    }

    public function salvar()
    {
		try {

            $tipoServicoData = new tb_tipo_servico;

            $Tipos = array(
                'Recepção',
                'Buffet',
                'Foto/Vídeo',
                'Música',
                'Animação',
                'Decoração',
                'Maquiagem',
            );

            foreach ($Tipos as &$value) {
                
                $tipoServicoData = array(
                    'nome'=> $value
                );
                $this->tipoServico->create($tipoServicoData);
            }

			$return = ['data' => ['msg' => 'Tipos de serviços inseridos com sucesso!']];
			return response()->json($return);

		} catch (\Exception $e) {
			if(config('app.debug')) {
				return response()->json(ApiError::errorMensagem($e->getMessage(), 1010), 500);
			}
			return response()->json(ApiError::errorMensagem('Houve um erro ao realizar operação de salvar', 1010),  500);
		}
    }

    public function listarCidades()
    {
        try{

            $cidades = DB::table('tb_fornecedores')->distinct()->select('cidade')->groupBy('cidade')->get();
            return response()->json($cidades);
        
        } catch (\Exception $e) {
			if(config('app.debug')) {
				return response()->json(ApiError::errorMensagem($e->getMessage(), 1011),  500);
			}
			return response()->json(ApiError::errorMensagem('Houve um erro ao realizar operação de atualizar', 1011), 500);
		}
    }

    public function listarBairros()
    {
        try{

            $bairros = DB::table('tb_fornecedores')->distinct()->select('bairro')->groupBy('bairro')->get();
            return response()->json($bairros);
            
        } catch (\Exception $e) {
			if(config('app.debug')) {
				return response()->json(ApiError::errorMensagem($e->getMessage(), 1011),  500);
			}
			return response()->json(ApiError::errorMensagem('Houve um erro ao realizar operação de atualizar', 1011), 500);
		}
    }
}
