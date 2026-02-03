<?php
$idConvenio = $_GET['idConvenio'];
$uploadDir = 'anexos/' . $idConvenio . '/';

// Verifica se o diretório existe
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    $files = array_diff($files, array('.', '..'));

    // Retorna a lista de arquivos como JSON
    echo json_encode(['files' => $files]);
} else {
    echo json_encode(['files' => []]);
}
?>
