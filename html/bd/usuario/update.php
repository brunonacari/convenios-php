<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';
include_once('../../protectAdmin.php');

$matricula = $_POST['matricula'];
$idPerfis = $_POST['perfilUsuario'];

$email = $_POST['email'];
$idUsuario = $_POST['idUsuario'];

// echo($idUsuario);
// print_r($idPerfis);
// echo($email);
// exit();

$login = $_SESSION['login'];

if (isset($idPerfis) && is_array($idPerfis)) {
    foreach ($idPerfis as $idPerfil) {
        $queryInsertidPerfil = $pdoCAT->prepare("UPDATE USUARIO 
                                                    SET ID_CONVENIO = $idPerfil 
                                                        , EMAIL_USU = '$email'
                                                    WHERE ID_USU = :idUsuario");
        // $queryInsertidPerfil->bindParam(':idPerfil', $idPerfil, PDO::PARAM_INT);
        $queryInsertidPerfil->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
        // print_r($queryInsertidPerfil);
        // exit();
        $queryInsertidPerfil->execute();
    }
}

$_SESSION['msg'] = "Usuário atualizado com sucesso.";

$_SESSION['redirecionar'] = '../../envio.php?emailAtivarUsuario='.$email;
$tela = 'Usuario';
$acao = 'Atualizado';
$evento = $idUsuario;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$evento");