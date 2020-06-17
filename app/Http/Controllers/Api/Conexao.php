<?php
define('HOST', '127.0.0.1');
define('USUARIO', 'root');
define('SENHA', 'ALMS_eventos2020');
define('DB', 'underline_eventos');
$dsn = "mysql:host=127.0.0.1;port=3306;dbname=underline_eventos";
$conexao = mysqli_connect(HOST, USUARIO, SENHA, DB) or die ('Não foi possível conectar');
//$conexao = new PDO(mysqli_connect(HOST, USUARIO, SENHA, DB) or die ('Não foi possível conectar'));
//$conexao_imagem = new PDO('mysql:host='.HOST .';dbname='.DB, USUARIO, SENHA);
//     //"mysql:host=localhost;dbname=exercicio", "root", "senha");
try 
{
    // Conectando
    $pdo = new PDO($dsn, USUARIO, SENHA);
} 
catch (PDOException $e) 
{
    // Se ocorrer algum erro na conexão
    die($e->getMessage());
}