<?php
session_start();
include_once 'bd/conexao.php';

// Definindo cabeçalhos de resposta para depuração
header('Content-Type: application/json');

// $_SESSION['msg'] = 'teste';

$file = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_SPECIAL_CHARS);
$directory = filter_input(INPUT_GET, 'directory', FILTER_SANITIZE_SPECIAL_CHARS);
$idConvenio = filter_input(INPUT_GET, 'idConvenio', FILTER_SANITIZE_SPECIAL_CHARS);
$idUsuario = filter_input(INPUT_GET, 'idUsuario', FILTER_SANITIZE_SPECIAL_CHARS);
// $dtExcAnexo = filter_input(INPUT_GET, 'dtExcAnexo', FILTER_SANITIZE_SPECIAL_CHARS);

$fullpath = $directory . '/' . $file;

// Logging para depuração
error_log("Tentando excluir o arquivo: $fullpath");

// Verifique se o arquivo existe no diretório
if (file_exists($fullpath)) {
    // Tente excluir o arquivo
    if (unlink($fullpath)) {
        $response = array('status' => 'success', 'message' => "Arquivo '$file' excluído com sucesso.");
    } else {
        $response = array('status' => 'error', 'message' => "Erro ao excluir o arquivo: $fullpath.");
    }
} else {
    $response = array('status' => 'error', 'message' => "Arquivo não encontrado: $fullpath.");
}

echo json_encode($response);

if (!isset($dtExcAnexo)) {
    $login = $_SESSION['login'];
    $acao = 'Excluir Anexo: ' . $file;
    $idEvento = $idConvenio;
    $queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA (login, data, tela, acao, idEvento) VALUES('$login', GETDATE(), 'Licitação', '$acao', $idEvento)");
} else {
    $login = $_SESSION['login'];
    $acao = 'Restaurar Anexo: ' . $file;
    $idEvento = $idConvenio;
    $queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA (login, data, tela, acao, idEvento) VALUES('$login', GETDATE(), 'Licitação', '$acao', $idEvento)");
}

?>
