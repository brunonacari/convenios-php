<?php

session_start();

include_once '../includes/header.inc.php';
include_once 'conexao.php';
include_once '../redirecionar.php';

// include_once '../api.php';

$login      = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
// $senha      = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);
$senha = trim($_POST['senha']); // Sem filtro especial para permitir caracteres especiais
$loginADM   = null;

if (strpos($login, '@') !== false && !empty($login) && !empty($senha)) {

    $querySelect3 = "SELECT C.NOME_CONVENIO, U.ID_CONVENIO
                        FROM USUARIO U
                        LEFT JOIN CONVENIOS C ON C.ID_CONVENIO = U.ID_CONVENIO
                        WHERE EMAIL_USU = :login";

    $stmt = $pdoCAT->prepare($querySelect3);
    $stmt->bindParam(':login', $login, PDO::PARAM_STR);
    $stmt->execute();

    // var_dump($querySelect2);
    // exit();
    while ($registros = $stmt->fetch(PDO::FETCH_ASSOC)) :
        $idConvenio  = $registros['ID_CONVENIO'];
        $nomeConvenio  = $registros['NOME_CONVENIO'];

        $perfilUsuario[] = array(
            'idConvenio' => $registros['ID_CONVENIO']
        );
    endwhile;


    $querySelect2 = "SELECT U.ID_USU, U.EMAIL_USU, U.ID_CONVENIO, C.NOME_CONVENIO, U.SENHA_USU
                        FROM USUARIO U
                        LEFT JOIN CONVENIOS C ON C.ID_CONVENIO = U.ID_CONVENIO
                        WHERE U.EMAIL_USU = :login
                        AND U.STATUS_USU = 'A'";

    if ($nomeConvenio != 'ADMINISTRADOR') {
        $querySelect2 .= " AND C.DT_FIM_CONVENIO >= GETDATE()";
    }

    $querySelect2 .= " AND U.ID_CONVENIO IS NOT NULL";

    $stmt = $pdoCAT->prepare($querySelect2);
    $stmt->bindParam(':login', $login, PDO::PARAM_STR);
    $stmt->execute();

    // var_dump($querySelect2);
    // exit();
    while ($registros = $stmt->fetch(PDO::FETCH_ASSOC)) :
        $idUsuario = $registros['ID_USU'];
        $emailUsuario = $registros['EMAIL_USU'];
        $nmUsuario = $registros['NM_USU'];
        $senhaBanco  = $registros['SENHA_USU'];
        $idConvenio  = $registros['ID_CONVENIO'];

        $perfilUsuario[] = array(
            'idConvenio' => $registros['ID_CONVENIO'],
            'nomeConvenio' => $registros['NOME_CONVENIO']
        );
    endwhile;

    // var_dump($senha);
    // var_dump($senhaBanco);
    // exit();

    // Comparar a senha calculada com a senha armazenada no banco de dados
    if ($stmt && password_verify($senha, $senhaBanco)) {
        $_SESSION['sucesso'] = 1;
        $_SESSION['login'] = $login;
        $_SESSION['perfil'] = $perfilUsuario;
        $_SESSION['email'] = $emailUsuario;
        $_SESSION['idUsuario'] = $idUsuario;
    } else {
        $_SESSION['sucesso'] = 0;

        if ($idConvenio > 0) {
            $_SESSION['msg'] = 'Usuário ou Senha inválidos';
        } else {
            $_SESSION['msg'] = 'Aguardando liberação da área responsável';
        }
    }
    if ($_SESSION['sucesso'] == 1) {
        $_SESSION['redirecionar'] = '../index.php';
    } else {
        $_SESSION['redirecionar'] = '../login.php';
    }
    $login = $_SESSION['login'];
    $tela = 'Login';
    $acao = 'Login';
    $idEvento = 0;
    redirecionar("../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
    exit();
}

$ldap_server = 'cesan.com.br';
$dominio = '@cesan.com.br'; //Dominio local ou global
$user = $login . $dominio;
$ldap_porta = '389';
$ldap_porta = '389';
$ldapcon = ldap_connect($ldap_server, $ldap_porta) or die('Could not connect to LDAP server.');

if ($ldapcon) {
    ldap_set_option($ldapcon, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapcon, LDAP_OPT_REFERRALS, 0);

    $bind = ldap_bind($ldapcon, $user, $senha); // Não escapar a senha aqui

    // verify binding
    if ($bind) {
        $_SESSION['sucesso'] = 1;

        $_SESSION['login'] = $login;

        $querySelect2 = "SELECT U.ID_USU, U.EMAIL_USU, U.ID_CONVENIO, C.NOME_CONVENIO
                            FROM USUARIO U
							left join CONVENIOS C ON C.ID_CONVENIO = U.ID_CONVENIO
                         WHERE U.LGN_USU = '$login' AND U.STATUS_USU = 'A'";

        $querySelect = $pdoCAT->query($querySelect2);
        $perfilUsuario = array();
        $emailUsuario = null;
        $idUsuario = null;

        while ($registros = $querySelect->fetch(PDO::FETCH_ASSOC)) {
            $idUsuario = $registros['ID_USU'];
            $emailUsuario = $registros['EMAIL_USU'];
            $idConvenio = $registros['ID_CONVENIO'];
            // Adicione cada ID_TIPO_LICITACAO ao array $perfilUsuario
            $perfilUsuario[] = array(
                'idConvenio' => $registros['ID_CONVENIO'],
                'nomeConvenio' => $registros['NOME_CONVENIO']
            ); 
        }

        $_SESSION['perfil'] = $perfilUsuario;
        $_SESSION['email'] = $emailUsuario;
        $_SESSION['idUsuario'] = $idUsuario;

        foreach ($_SESSION['perfil'] as $perfil) {
            if ($perfil['nomeConvenio'] == 'ADMINISTRADOR') {
                $_SESSION['isAdmin'] = 1;
            }
        }
        $_SESSION['msg'] = 'Aguardando liberação da área responsável';

        //se o usuário existir no sistema e possuir um convênio associado
        if ($idConvenio > 0) {
            $_SESSION['redirecionar'] = '../index.php';
            $acao = 'Login';
        } else {
            if ($idUsuario > 0) { //se o usuário existir no sistema e NÃO possuir um convênio associado
                $_SESSION['msg'] = 'Aguardando liberação da área responsável';
            } else { //se o usuário NÃO existir no sistema
                $_SESSION['msg'] = 'Usuário não cadastrado no sistema';
            }
            $_SESSION['redirecionar'] = '../login.php';
            $acao = 'Erro ao logar';
        }

        $login = $_SESSION['login'];
        $tela = 'Login';
        $idEvento = 0;
        redirecionar("../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$idEvento");
    } else {

        $_SESSION['sucesso'] = 0;

        $_SESSION['perfil'] = 0;

        $_SESSION['login'] = '';

        $_SESSION['idLogin'] = 0;

        $_SESSION['email'] = '';

        $_SESSION['msg'] = 'Usuário ou senha inválidos';

        $_SESSION['redirecionar'] = '../login.php';

        $tela = 'Login';
        $acao = 'Erro ao logar';
        $evento = 0;
        redirecionar("../../log.php?login=$login&tela=$tela&acao=$acao&idEvento=$evento");
    }
}
