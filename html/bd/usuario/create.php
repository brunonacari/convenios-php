<?php

session_start();
include_once '../conexao.php';
include_once '../../redirecionar.php';

// ============================================
// 1. RATE LIMITING - Limitar tentativas por IP
// ============================================
function verificarRateLimit($pdoCAT, $ip) {
    $stmt = $pdoCAT->prepare("
        SELECT COUNT(*) as tentativas 
        FROM TENTATIVAS_REGISTRO 
        WHERE IP_ORIGEM = :ip 
        AND DATA_TENTATIVA > DATEADD(HOUR, -1, GETDATE())
    ");
    $stmt->bindParam(':ip', $ip);
    $stmt->execute();
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Máximo 3 tentativas por hora
    if ($resultado['tentativas'] >= 3) {
        return false;
    }
    
    // Registrar tentativa
    $stmtInsert = $pdoCAT->prepare("
        INSERT INTO TENTATIVAS_REGISTRO (IP_ORIGEM, DATA_TENTATIVA) 
        VALUES (:ip, GETDATE())
    ");
    $stmtInsert->bindParam(':ip', $ip);
    $stmtInsert->execute();
    
    return true;
}

function validarTelefone($telefone) {
    // Remove caracteres não numéricos
    $telefone = preg_replace('/[^0-9]/', '', $telefone);
    // Verifica se tem 10 ou 11 dígitos (DDD + número)
    return strlen($telefone) >= 10 && strlen($telefone) <= 11;
}

function sanitizarInput($valor) {
    return htmlspecialchars(strip_tags(trim($valor)), ENT_QUOTES, 'UTF-8');
}

$ip = $_SERVER['REMOTE_ADDR'];

// 1. Verificar rate limiting
if (!verificarRateLimit($pdoCAT, $ip)) {
    echo "<script>alert('Muitas tentativas de registro. Tente novamente em 1 hora.');</script>";
    echo "<script>window.location.href='../../login.php';</script>";
    exit();
}

$camposObrigatorios = ['nomeUsuarioNovo', 'orgao', 'senhaUsuarioNovo', 'senhaUsuarioNovo2', 'emailUsuarioNovo', 'telUsuarioNovo'];
foreach ($camposObrigatorios as $campo) {
    if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
        echo "<script>alert('Todos os campos são obrigatórios.');</script>";
        echo "<script>window.history.back();</script>";
        exit();
    }
}

$nmUsuario = sanitizarInput($_POST['nomeUsuarioNovo']);
$orgao = sanitizarInput($_POST['orgao']);
$senhaUsuario = $_POST['senhaUsuarioNovo'];
$senhaUsuario2 = $_POST['senhaUsuarioNovo2'];
$emailUsuario = filter_var(trim($_POST['emailUsuarioNovo']), FILTER_VALIDATE_EMAIL);
$telUsuarioNovo = sanitizarInput($_POST['telUsuarioNovo']);

// 5. Validações específicas
if (!$emailUsuario) {
    echo "<script>alert('E-mail inválido!');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

if (!validarTelefone($telUsuarioNovo)) {
    echo "<script>alert('Telefone inválido!');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

$senhaHash = password_hash($senhaUsuario, PASSWORD_DEFAULT);

//verifica se o usuário digitou as senhas iguais
if ($senhaUsuario != $senhaUsuario2) {
    echo "<script>alert('Senhas diferentes!');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

$querySelectPerfil = "SELECT EMAIL_USU FROM USUARIO WHERE EMAIL_USU = :emailUsuario";
$stmt = $pdoCAT->prepare($querySelectPerfil);
$stmt->bindParam(':emailUsuario', $emailUsuario);
$stmt->execute();
$emailExists = $stmt->fetch(PDO::FETCH_ASSOC);

// verifica se o e-mail digitado já existe
if ($emailExists) {
    echo "<script>alert('E-mail já cadastrado!');</script>";
    echo "<script>window.history.back();</script>";
    exit();
}

// $queryAdmin2 = "INSERT INTO USUARIO VALUES (0, '$nmUsuario', '$emailUsuario',  GETDATE(), 'A', 'externo', '$emailUsuario', NULL, '$senhaHash', '$orgao', '$telUsuarioNovo')";
// $querySelectPerfil2 = $pdoCAT->query($queryAdmin2);

$queryAdmin2 = "INSERT INTO USUARIO 
        (MAT_USU, NM_USU, EMAIL_USU, DT_CADASTRO_USU, STATUS_USU, 
         LGN_CRIADOR_USU, LGN_USU, ID_CONVENIO, SENHA_USU, ORGAO_USU, TEL_USU) 
        VALUES 
        (0, :nmUsuario, :emailUsuario, GETDATE(), 'A', 'externo', 
         :email2Usuario, NULL, :senhaHash, :orgao, :telUsuario)";

$stmt2 = $pdoCAT->prepare($queryAdmin2);
$stmt2->bindParam(':nmUsuario', $nmUsuario, PDO::PARAM_STR);
$stmt2->bindParam(':emailUsuario', $emailUsuario, PDO::PARAM_STR);
$stmt2->bindParam(':email2Usuario', $emailUsuario, PDO::PARAM_STR);
$stmt2->bindParam(':senhaHash', $senhaHash, PDO::PARAM_STR);
$stmt2->bindParam(':orgao', $orgao, PDO::PARAM_STR);
$stmt2->bindParam(':telUsuario', $telUsuarioNovo, PDO::PARAM_STR);
$stmt2->execute();
    
// $debugSQL = $queryAdmin2;
// $debugSQL = str_replace(':nmUsuario', "'{$nmUsuario}'", $debugSQL);
// $debugSQL = str_replace(':emailUsuario', "'{$emailUsuario}'", $debugSQL);
// $debugSQL = str_replace(':email2Usuario', "'{$emailUsuario}'", $debugSQL);
// $debugSQL = str_replace(':senhaHash', "'{$senhaHash}'", $debugSQL);
// $debugSQL = str_replace(':orgao', "'{$orgao}'", $debugSQL);
// $debugSQL = str_replace(':telUsuario', "'{$telUsuarioNovo}'", $debugSQL);

// echo "<pre>" . $debugSQL . "</pre>";

$_SESSION['msg'] = "Usuário cadastrado com sucesso.";

$_SESSION['redirecionar'] = '../../envio.php?emailUsuarioNovo='.$emailUsuario;
$login = $_SESSION['login'];
$tela = 'Login';
$acao = 'Usuário cadastrado: ' . $nmUsuario;
$idEvento = 1;
redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
