<?php
session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$pattern = "/^\d{4}-\d{2}-\d{2}$/";
$patternTime = "/^\d{2}:\d{2}$/";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nomeConvenio = $_POST["nomeConvenio"];
    $numConvenio = $_POST["numConvenio"];

    $dtIniConvenio = $_POST["dtIniConvenio"];
    $dtFimConvenio = $_POST["dtFimConvenio"];

    $dtIniConvenio = (new DateTime($dtIniConvenio))->format('Y-m-d');
    $dtFimConvenio = (new DateTime($dtFimConvenio))->format('Y-m-d');

    $nomeContatoAdm = $_POST["nomeContatoAdm"];
    $emailContatoAdm = $_POST["emailContatoAdm"];
    $telContatoAdm = $_POST["telContatoAdm"];
    $nomeContatoTI = $_POST["nomeContatoTI"];
    $emailContatoTI = $_POST["emailContatoTI"];
    $telContatoTI = $_POST["telContatoTI"];

    $login = $_SESSION["login"];

    $queryInsert = $pdoCAT->query("INSERT INTO [convenios].[dbo].[CONVENIOS] VALUES ('$nomeConvenio', '$numConvenio', '$dtIniConvenio', '$dtFimConvenio','$nomeContatoAdm', '$emailContatoAdm', '$telContatoAdm', '$nomeContatoTI', '$emailContatoTI' , '$telContatoTI')");

    if ($queryInsert) {
        $_SESSION['msg'] = "Convênio cadastrado com sucesso.";
    } else {
        $_SESSION['msg'] = "Erro ao cadastrar convênio.";
    }

    $_SESSION['redirecionar'] = '../../cadConvenios.php';
    $login = $_SESSION['login'];
    $tela = 'Convenios';
    $acao = 'CRIADO';
    $idEvento = 1;
    redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
} else {
    $_SESSION['msg'] = "Método inválido.";
    header('Location: ../cadConvenio.php');
}
