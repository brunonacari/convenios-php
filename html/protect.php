<?php
include_once 'redirecionar.php';

if (!isset($_SESSION)) {
    session_start();
}

// echo "<script>alert($idLicitacao);</script>;";

// Caso o usuário tente acessar qq trecho do sistema sem login realizado, o sistema direciona para a tela de LOGOUT
if ($_SESSION['sucesso'] != 1)
{
    $_SESSION['redirecionar'] = 'index.php';
    redirecionar($_SESSION['redirecionar']);
}