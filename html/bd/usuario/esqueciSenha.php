<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

$emailUsuario = $_POST['emailEsqueciSenha'];

$querySelect2 = "SELECT TOP 1 ID_USU
  FROM [Convenios].[dbo].[USUARIO]
  WHERE EMAIL_USU = :emailUsuario
  AND STATUS_USU = 'A'";

$querySelect = $pdoCAT->prepare($querySelect2);
$querySelect->bindParam(':emailUsuario', $emailUsuario);
$querySelect->execute();


// var_dump($querySelect->rowCount());
// exit();
// Verifica se a consulta trouxe algum resultado
if ($querySelect->rowCount() != 0) {
    $registros = $querySelect->fetch(PDO::FETCH_ASSOC);
    $idUsuario = $registros['ID_USU'];

    $_SESSION['redirecionar'] = '../../envio.php?emailUsuario=' . $emailUsuario;
    $login = $_SESSION['login'];
    $tela = 'Login';
    $acao = 'Esqueci senha: ' . $emailUsuario;
    $idEvento = 0;
    redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
} else {
    $_SESSION['msg'] = 'E-mail não cadastrado';
    redirecionar("../../login.php");
}
