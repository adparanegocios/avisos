<?php

define('DB_HOST', "");
define('DB_USER', "");
define('DB_PASSWORD', "");
define('DB_NAME', "");
define('DB_DRIVER', "sqlsrv");

define ( 'HOST_EMAIL', '' );
define ( 'USER_EMAIL', '' );
define ( 'PASS_EMAIL', '' );
define ( 'PORT_EMAIL', '25' );
define ( 'PERCENTAGEM_LIMITE', '80' );

require_once "conexao.php";
require_once "phpmailer/src/PHPMailer.php";
require_once "phpmailer/src/SMTP.php";
require_once "util.php";

try{
    $conexao = Conexao::getConnection();
}catch(Exception $e){
    echo $e->getMessage();
    exit;
}

$vetEmails = array(
    "ferias" => array(
        "",
        ""
    ),
    "demissao" => array(
        "",
    ),
    "admissao" => array(
        "",
        ""
    ),
    "afastamentos" => array(
        "",
        ""
    )
);

?>