<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$idConvenio = filter_input(INPUT_GET, 'idConvenio', FILTER_SANITIZE_NUMBER_INT);

$queryUpdateCriterio = "UPDATE [convenios].[dbo].[convenioS] SET DT_FIM_CONVENIO = GETDATE() WHERE ID_CONVENIO = $idConvenio";
$queryUpdateCriterio2 = $pdoCAT->query($queryUpdateCriterio);

$_SESSION['msg'] = "Criterio desativado com sucesso.";

$_SESSION['redirecionar'] = '../consultarConvenios.php';
$login = $_SESSION['login'];
$tela = 'Convenios';
$acao = 'DESATIVADO';
$idEvento = $idConvenio;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");