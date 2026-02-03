<?php
session_start();

include_once '../conexao.php';
include_once '../../redirecionar.php';

include_once('../../protectAdmin.php');

$idConvenio = filter_input(INPUT_POST, 'idConvenio', FILTER_SANITIZE_NUMBER_INT);
$nomeConvenio = filter_input(INPUT_POST, 'nomeConvenio', FILTER_SANITIZE_SPECIAL_CHARS);
$numConvenio = filter_input(INPUT_POST, 'numConvenio', FILTER_SANITIZE_SPECIAL_CHARS);
$dtIniConvenio = filter_input(INPUT_POST, 'dtIniConvenio', FILTER_SANITIZE_SPECIAL_CHARS);
$dtFimConvenio = filter_input(INPUT_POST, 'dtFimConvenio', FILTER_SANITIZE_SPECIAL_CHARS);
$nomeContatoAdm = filter_input(INPUT_POST, 'nomeContatoAdm', FILTER_SANITIZE_SPECIAL_CHARS);
$emailContatoAdm = filter_input(INPUT_POST, 'emailContatoAdm', FILTER_SANITIZE_EMAIL);
$telContatoAdm = filter_input(INPUT_POST, 'telContatoAdm', FILTER_SANITIZE_SPECIAL_CHARS);
$nomeContatoTI = filter_input(INPUT_POST, 'nomeContatoTI', FILTER_SANITIZE_SPECIAL_CHARS);
$emailContatoTI = filter_input(INPUT_POST, 'emailContatoTI', FILTER_SANITIZE_EMAIL);
$telContatoTI = filter_input(INPUT_POST, 'telContatoTI', FILTER_SANITIZE_SPECIAL_CHARS);

$dtIniConvenio = (new DateTime($dtIniConvenio))->format('Y-m-d');
$dtFimConvenio = (new DateTime($dtFimConvenio))->format('Y-m-d');

$queryUpdate = "UPDATE [CONVENIOS].[dbo].[CONVENIOS]
                SET NOME_CONVENIO = :nomeConvenio,
                    NUM_CONVENIO = :numConvenio,
                    DT_INI_CONVENIO = :dtIniConvenio,
                    DT_FIM_CONVENIO = :dtFimConvenio,
                    NOME_CONTATO_ADM = :nomeContatoAdm,
                    EMAIL_CONTATO_ADM = :emailContatoAdm,
                    TEL_CONTATO_ADM = :telContatoAdm,
                    NOME_CONTATO_TI = :nomeContatoTI,
                    EMAIL_CONTATO_TI = :emailContatoTI,
                    TEL_CONTATO_TI = :telContatoTI
                WHERE ID_CONVENIO = :idConvenio";

$stmt = $pdoCAT->prepare($queryUpdate);
$stmt->bindParam(':idConvenio', $idConvenio, PDO::PARAM_INT);
$stmt->bindParam(':nomeConvenio', $nomeConvenio, PDO::PARAM_STR);
$stmt->bindParam(':numConvenio', $numConvenio, PDO::PARAM_STR);
$stmt->bindParam(':dtIniConvenio', $dtIniConvenio, PDO::PARAM_STR);
$stmt->bindParam(':dtFimConvenio', $dtFimConvenio, PDO::PARAM_STR);
$stmt->bindParam(':nomeContatoAdm', $nomeContatoAdm, PDO::PARAM_STR);
$stmt->bindParam(':emailContatoAdm', $emailContatoAdm, PDO::PARAM_STR);
$stmt->bindParam(':telContatoAdm', $telContatoAdm, PDO::PARAM_STR);
$stmt->bindParam(':nomeContatoTI', $nomeContatoTI, PDO::PARAM_STR);
$stmt->bindParam(':emailContatoTI', $emailContatoTI, PDO::PARAM_STR);
$stmt->bindParam(':telContatoTI', $telContatoTI, PDO::PARAM_STR);

if ($stmt->execute()) {
    $_SESSION['msg'] = "Convênio atualizado com sucesso.";
} else {
    $_SESSION['msg'] = "Erro ao atualizar convênio.";
}

$_SESSION['redirecionar'] = '../../consultarConvenios.php';
$login = $_SESSION['login'];
$tela = 'Convenios';
$acao = 'ATUALIZADO';
$idEvento = $idConvenio;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
?>
