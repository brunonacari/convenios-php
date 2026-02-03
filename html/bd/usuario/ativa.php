<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include_once('../../protectAdmin.php');

$email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_SPECIAL_CHARS);

$queryAdmin = "SELECT ID_USU,EMAIL_USU FROM USUARIO WHERE EMAIL_USU LIKE '$email' AND STATUS_USU like 'I'";

$queryDesativar = $pdoCAT->query($queryAdmin);

while ($registros = $queryDesativar->fetch(PDO::FETCH_ASSOC)) :
    $existeUsuario = $registros['EMAIL_USU'];
    $idUsuario = $registros['ID_USU'];
endwhile;

if (isset($existeUsuario)) {
    $queryUpdate = $pdoCAT->query("UPDATE USUARIO SET STATUS_USU = 'A' WHERE EMAIL_USU like '$email'");

    $_SESSION['msg'] = "Usuário ativado com sucesso.";

    $_SESSION['redirecionar'] = '../../consultarUsuario.php';
    $login = $_SESSION['login'];
    $tela = 'Usuario';
    $acao = 'Ativado: '.$email;
    $evento = $idUsuario;
    redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$evento");

} else {

    $_SESSION['msg'] = "Usuário <strong>não</strong> é Administrador.";
    header("Location: ../../consultarUsuario.php");
}
