<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

session_start();

//Load Composer's autoloader
require '../vendor/autoload.php';
include_once 'bd/conexao.php';

$emailUsuario = filter_input(INPUT_GET, 'emailUsuario', FILTER_SANITIZE_SPECIAL_CHARS);
$emailUsuarioNovo = filter_input(INPUT_GET, 'emailUsuarioNovo', FILTER_SANITIZE_SPECIAL_CHARS);
$emailAtivarUsuario = filter_input(INPUT_GET, 'emailAtivarUsuario', FILTER_SANITIZE_SPECIAL_CHARS);

$mail = new PHPMailer(true);

$protocolo = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$porta = $_SERVER['SERVER_PORT'];

$linkLogin = "$protocolo://$host/login.php";
$linkEsqueciSenha = "$protocolo://$host/trocaSenhaUsuario.php";

try {
    //Server settings
    // // // // // // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'app-mail.sistemas.cesan.com.br';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = false;                                   //Enable SMTP authentication
    $mail->Username   = 'convenios@cesan.com.br';                     //SMTP username
    $mail->Port       = 25;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
    $mail->SMTPAutoTLS = false;

    // $mail->isSMTP();
    // $mail->Host       = getenv('SMTP_HOST');
    // $mail->Port       = getenv('SMTP_PORT');
    // $mail->SMTPAuth   = true;
    // $mail->Username   = getenv('SMTP_USERNAME');
    // $mail->Password   = getenv('SMTP_PASSWORD');
    // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    // $mail->SMTPAutoTLS = false;

    //Recipients
    $mail->setFrom('convenios.ti@cesan.com.br', 'CESAN - Portal de Convênios');

    // SE FOR RECUPERAÇÃO DE SENHA =======================================================================================================================
    if (isset($emailUsuario)) {

        // Gerar uma senha temporária aleatória
        $novaSenha = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10);

        // Criptografar a nova senha temporária
        $senhaHash = password_hash($novaSenha, PASSWORD_DEFAULT);

        // Atualizar a senha no banco de dados
        $queryUpdateSenha = "UPDATE USUARIO SET SENHA_USU = :senha WHERE EMAIL_USU = :email";
        $stmt = $pdoCAT->prepare($queryUpdateSenha);
        $stmt->bindParam(':senha', $senhaHash);
        $stmt->bindParam(':email', $emailUsuario);
        $stmt->execute();

        $querySelectPerfil = "SELECT * FROM USUARIO WHERE EMAIL_USU LIKE '$emailUsuario'";
        $querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
        while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
            $nmUsuario = $registros['NM_USU'];
            $email = $registros['EMAIL_USU'];

            $mail->addAddress($email, 'Convênios | CESAN');

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->CharSet = 'UTF-8';                      // Set charset to UTF-8
            $mail->Encoding = 'base64';
            $mail->Subject = 'Recuperação de Senha';
            $mail->Body    = '<b>Solicitação de Recuperação de Senha do Portal de Convênios da Cesan</b></br></br>';
            $mail->Body    .= ' <p>Nome do Solicitante: <b>' . $nmUsuario . '</b></br></p>';
            $mail->Body    .= ' <p>E-mail de Contato: <b>' . $email . '</b></br></p>';
            $mail->Body    .= ' <p>Senha: <b>' . $novaSenha . '</b></br></p>';
            $mail->Body    .= ' <p>Link de acesso: <a href="' . $linkLogin . '">' . $linkLogin . '</a></br></p>';
            $mail->Body    .= ' <p>Após realizar o login no sistema, acesse o menu <a href="' . $linkEsqueciSenha . '">TROCAR SENHA</a> e troque sua senha.</br></p>';

            $mail->send();
        endwhile;

        $_SESSION['msg'] = "Senha TEMPORÁRIA enviada para o e-mail cadastrado.";

        echo "<script>location.href='login.php';</script>";

        if (!isset($email)) {
            $_SESSION['msg'] = "E-mail NÃO cadastrado.";

            echo "<script>location.href='login.php';</script>";
        }

    } else if (isset($emailUsuarioNovo)) {
        $querySelectPerfil = "SELECT * FROM USUARIO WHERE EMAIL_USU LIKE '$emailUsuarioNovo'";
        $querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
        while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
            $nmUsuario = $registros['NM_USU'];
            $email = $registros['EMAIL_USU'];
            $tel = $registros['TEL_USU'];
            $orgao = $registros['ORGAO_USU'];

            $mail->addAddress('convenios.ti@cesan.com.br', 'Portal de Convênios | CESAN');

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->CharSet = 'UTF-8';                      // Set charset to UTF-8
            $mail->Encoding = 'base64';
            $mail->Subject = 'Solicitação de Acesso';
            $mail->Body    = '<b>Solicitação de Acesso ao Portal de Convênios da Cesan</b></br></br>';
            $mail->Body    .= ' <p>Nome do Solicitante: <b>' . $nmUsuario . '</b></br></p>';
            $mail->Body    .= ' <p>E-mail de Contato: <b>' . $email . '</b></br></p>';
            $mail->Body    .= ' <p>Telefone de Contato: <b>' . $tel . '</b></br></p>';
            $mail->Body    .= ' <p>Órgão do Solicitante: <b>' . $orgao . '</b></br></p>';
            $mail->Body    .= ' <p>Entre em contato com o solicitante para liberar o acesso ao Portal de Convênios da Cesan.</br></p>';

            $mail->send();
        endwhile;

        $_SESSION['msg'] = "Solicitação enviada com sucesso. A Cesan entrará em contato para confirmar alguns dados e liberar seu acesso.";

        echo "<script>location.href='login.php';</script>";

        if (!isset($email)) {
            $_SESSION['msg'] = "E-mail NÃO cadastrado..";

            echo "<script>location.href='login.php';</script>";
        }
    } else if (isset($emailAtivarUsuario)) {
        $querySelectPerfil = "SELECT * FROM USUARIO WHERE EMAIL_USU LIKE '$emailAtivarUsuario'";
        $querySelectPerfil2 = $pdoCAT->query($querySelectPerfil);
        while ($registros = $querySelectPerfil2->fetch(PDO::FETCH_ASSOC)) :
            $nmUsuario = $registros['NM_USU'];
            $email = $registros['EMAIL_USU'];
            $tel = $registros['TEL_USU'];
            $orgao = $registros['ORGAO_USU'];

            $mail->addAddress($email, 'Portal de Convênios | CESAN');

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->CharSet = 'UTF-8';                      // Set charset to UTF-8
            $mail->Encoding = 'base64';
            $mail->Subject = 'Solicitação de Acesso';
            $mail->Body    = '<b>Acesso concedido ao Portal de Convênios da Cesan</b></br></br>';
            $mail->Body    .= ' <p>Nome do Solicitante: <b>' . $nmUsuario . '</b></br></p>';
            $mail->Body    .= ' <p>E-mail de Contato: <b>' . $email . '</b></br></p>';
            $mail->Body    .= ' <p>Link de Acesso: <a href="https://convenios.sistemas.cesan.com.br/">Clique aqui!</a></a></br></p>';
            $mail->Body    .= ' <p>Acesso concedido ao Portal de Convênios da Cesan.</br></p>';

            $mail->send();
        endwhile;

        $_SESSION['msg'] = "Acesso concedido";

        echo "<script>location.href='consultarUsuario.php';</script>";

        if (!isset($email)) {
            $_SESSION['msg'] = "E-mail NÃO cadastrado...";

            echo "<script>location.href='login.php';</script>";
        }
    }
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
