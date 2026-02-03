<?php
session_start();
include_once 'bd/conexao.php';

// Para depuração, exibindo erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$idConvenio = $_POST['idConvenio'];
$idUsuario = $_POST['idUsuario'];

if (isset($idUsuario)) {
    $uploadDir = 'anexos/usuario/' . $idUsuario . "/";
} else {
    $uploadDir = 'anexos/' . $idConvenio . "/";
}

echo "<script>alert($uploadDir);</script>";

if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Array para armazenar os nomes dos arquivos enviados
$uploadedFiles = [];

// Verifica se há arquivos enviados
if (!empty($_FILES['files']['name'])) {
    // Loop através de cada arquivo
    for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
        $fileName = $_FILES['files']['name'][$i];
        $filePath = $uploadDir . $fileName;

        // Verifica se o arquivo já existe no diretório
        $fileCount = 1;
        while (file_exists($filePath)) {
            // Renomeia o arquivo adicionando um número ao final
            $fileName = pathinfo($_FILES['files']['name'][$i], PATHINFO_FILENAME) . '_' . $fileCount . '.' . pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION);
            $filePath = $uploadDir . $fileName;
            $fileCount++;
        }

        // Move o arquivo para o diretório desejado
        if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $filePath)) {
            $uploadedFiles[] = $fileName;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Falha ao mover o arquivo: ' . $_FILES['files']['name'][$i]]);
            exit;
        }

        // Log de auditoria
        if (!isset($idConvenio)) {
            $idConvenio = $idUsuario;
            $tela = 'Usuario';
        } else {
            $tela = 'Convênio';
        }

        $login = $_SESSION['login'];
        $acao = 'Inserir Anexo: ' . $fileName;
        $idEvento = $idConvenio;
        $queryLOG = $pdoCAT->query("INSERT INTO AUDITORIA (login, data, tela, acao, idEvento) VALUES('$login', GETDATE(), '$tela', '$acao', $idEvento)");
    }
}

// Retorna os nomes dos arquivos enviados (pode ser processado mais adequadamente conforme necessário)
echo json_encode(['status' => 'success', 'uploadedFiles' => $uploadedFiles]);
