<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('API')->name('api.')->group(function(){

	Route::prefix('login')->group(function(){

		Route::post('/',  'LoginController@login')->name('login');
		
	});

	Route::prefix('clientes')->group(function(){

		Route::get('/', 'ClienteController@index')->name('clientes');
		
		Route::post('/',  'ClienteController@cadastrar')->name('cadastrar');
		
	});
	
	Route::prefix('fornecedor')->group(function(){

		Route::get('/', 'FornecedorController@index')->name('fornecedores');

		Route::post('/', 'FornecedorController@cadastrar')->name('cadastrar');

	});

	Route::prefix('filtro')->group(function(){

		Route::get('/tiposervico', 'FiltroController@listarTipoServico')->name('listarTipoServico');

		Route::get('/cidades', 'FiltroController@listarCidades')->name('listarCidades');

		Route::get('/bairros', 'FiltroController@listarBairros')->name('listarBairros');
		
	});

	Route::prefix('servicofornecedor')->group(function(){

		Route::get('/', 'ServicoFornecedorController@listar')->name('listar');

		Route::get('/destaques', 'ServicoFornecedorController@listarDestaque')->name('listarDestaque');

		Route::post('/', 'ServicoFornecedorController@cadastrar')->name('cadastrar');

		Route::post('/pesquisar', 'ServicoFornecedorController@pesquisar')->name('pesquisar');

		Route::post('/pesquisaprincipal', 'ServicoFornecedorController@pesquisaPrincipal')->name('pesquisaPrincipal');

		Route::put('/{id}', 'ServicoFornecedorController@update')->name('atualizar');

		Route::delete('/{id}', 'ServicoFornecedorController@delete')->name('deletar');
	});
});
