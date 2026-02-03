<?php
include_once 'redirecionar.php';

if (!isset($_SESSION)) {
    session_start();
}

// echo "<script>alert($idLicitacao);</script>;";

// Caso o usuário tente acessar qq trecho do sistema sem permissão, o sistema direciona para a tela de index
if (empty($_SESSION['perfil'])) {
    $_SESSION['redirecionar'] = 'logout.php';
    redirecionar($_SESSION['redirecionar']);
}
