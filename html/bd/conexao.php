<?php

$serverName =   getenv('DB_HOST');
$database =     getenv('DB_NAME');
$uid =          getenv('DB_USER');
$pwd =          getenv('DB_PASS');

// Evita enviar header se já foi enviado (para AJAX)
if (!headers_sent()) {
    header('Content-Type: text/html; charset=utf-8');
}

try {
    $pdoCAT = new PDO("sqlsrv:server=$serverName;Database=$database", $uid, $pwd);
    $pdoCAT->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erro de conexão pdoCAT: " . $e->getMessage());
    $pdoCAT = null;
}

$DB_HOST_SICAT=getenv('DB_HOST_SICAT');
$DB_NAME_SICAT=getenv('DB_NAME_SICAT');
$DB_USER_SICAT=getenv('DB_USER_SICAT');
$DB_PASS_SICAT=getenv('DB_PASS_SICAT');

try {
    $pdoSICAT = new PDO("sqlsrv:server=$DB_HOST_SICAT;Database=$DB_NAME_SICAT", $DB_USER_SICAT, $DB_PASS_SICAT);
    $pdoSICAT->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Erro de conexão pdoSICAT: " . $e->getMessage());
    $pdoSICAT = null;
}
?>

