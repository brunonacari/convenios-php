<?php
// Defina a URL da API de destino
$apiUrl = 'https://api.cargarh.hom.es.gov.br/v2/Carga/organizacao/281513630001470001';

// Obtenha o token de autorização do frontend (se necessário)
$authorizationHeader = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';

// Configure os cabeçalhos para a requisição à API
$options = [
    'http' => [
        'header' => [
            "Content-Type: application/json",
            "Authorization: $authorizationHeader"
        ],
        'method' => 'POST',
        'content' => file_get_contents("php://input") // Recebe o corpo JSON da requisição do frontend
    ]
];

$context = stream_context_create($options);

// Faz a requisição à API externa
$response = file_get_contents($apiUrl, false, $context);

if ($response === FALSE) {
    // Tratamento de erro: a requisição falhou
    http_response_code(500);
    echo json_encode(['error' => 'Falha ao conectar-se à API externa.']);
    exit;
}

// Retorna a resposta da API para o frontend
header('Content-Type: application/json');
echo $response;
