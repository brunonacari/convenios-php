<?php
include_once 'redirecionar.php';

if (!isset($_SESSION)) {
    session_start();
}
$isAdmin = null;
// Caso o usuário tente acessar qq trecho do sistema sem login realizado, o sistema direciona para a tela de LOGOUT
foreach ($_SESSION['perfil'] as $perfil) {
    if ($perfil['nomeConvenio'] == 'ADMINISTRADOR') {
        $isAdmin = 1;
    }
}

if ($isAdmin != 1){
    $_SESSION['redirecionar'] = '../../index.php';
    redirecionar($_SESSION['redirecionar']);
    exit();
}