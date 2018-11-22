<?php

require_once "global.php";

$sql = "
SELECT 
    E.CODCOLIGADA AS [CODIGO],
    UPPER(E.NOME) AS [EMPRESA],
    F.CHAPA AS [MATRICULA],
    UPPER(F.NOME) AS [NOME],
    UPPER(S.DESCRICAO) AS [SECAO],
    UPPER(C.NOME) AS [FUNCAO],
    CONVERT(VARCHAR(10),F.DATADEMISSAO,103) AS [DEMISSAO],
    CONVERT(VARCHAR(10),H.RECCREATEDON,103) AS [CADASTRO] 
FROM PFUNC F (NOLOCK)
INNER JOIN GCOLIGADA E (NOLOCK) ON (E.CODCOLIGADA = F.CODCOLIGADA)
INNER JOIN PSECAO S (NOLOCK) ON (S.CODIGO = F.CODSECAO AND S.CODCOLIGADA = F.CODCOLIGADA)
INNER JOIN PFUNCAO C (NOLOCK) ON (C.CODIGO = F.CODFUNCAO AND C.CODCOLIGADA = F.CODCOLIGADA)
INNER JOIN PFHSTSIT H (NOLOCK) ON (H.CHAPA = F.CHAPA AND H.CODCOLIGADA = F.CODCOLIGADA AND H.NOVASITUACAO = 'D')
WHERE
    F.CODCOLIGADA IN (1, 2, 3, 4, 5, 6, 8) AND
    F.CODSITUACAO = 'D' AND
    CONVERT(DATETIME,CONVERT(VARCHAR,H.RECCREATEDON,103),103) = CONVERT(DATETIME,CONVERT(VARCHAR,GETDATE(),103),103)
ORDER BY [CODIGO], [NOME], [DEMISSAO]
";

$rs = $conexao->prepare($sql);
$rs->execute();

$data_incio = date('d/m/Y');
$assunto = utf8_decode("[TOTVS] Demissões - Data: $data_incio");
$count = 0;

$conteudo = "
<html>
	<head>
		<title>Avisos</title>
	</head>
	<body>
		<div style='text-align: center;'>
			<strong>DEMISS&Otilde;ES - DATA: $data_incio</strong></div>
		<div style='text-align: justify;'>
			&nbsp;</div>
		<div style='text-align: justify;'>
			<table align='center' border='1' cellpadding='1' cellspacing='1'>
				<caption>
					<span style='font-size:20px;'><em><strong>Colaboradores</strong></em></span></caption>
				<thead>
					<tr>
						<th scope='col'>EMPRESA</th>
						<th scope='col'>MATR&Iacute;CULA</th>
						<th scope='col'>NOME</th>
						<th scope='col'>SE&Ccedil;&Atilde;O</th>
						<th scope='col'>FUN&Ccedil;&Atilde;O</th>
						<th scope='col'><font color='#ff0000'>DEMISS&Atilde;O</font></th>
						<th scope='col'>CADASTRO</th>
					</tr>
				</thead>
				<tbody>
";

while($row = $rs->fetch(PDO::FETCH_OBJ)){
    $empresa = utf8_decode($row->EMPRESA);
    $matricula = $row->MATRICULA;
    $nome = utf8_decode($row->NOME);
    $secao = utf8_decode($row->SECAO);
	$funcao = utf8_decode($row->FUNCAO);
	$demissao = $row->DEMISSAO;
	$cadastro = $row->CADASTRO;
    $conteudo .= "
    <tr>
		<td>$empresa</td>
		<td>$matricula</td>
		<td>$nome</td>
		<td>$secao</td>
		<td>$funcao</td>
		<td><span style='color:#ff0000;'>$demissao</span></td>
		<td>$cadastro</td>
	</tr>
    ";
    $count++;
}

$conteudo .= "
</tbody>
			</table>
			<p>&nbsp;</p>
		</div>
		<p>&nbsp;</p>
	</body>
</html>
";

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail -> charSet = "UTF-8";
$mail->SetLanguage ("br", "phpmailer/language/");
$mail->IsSMTP ();
$mail->Host = HOST_EMAIL;
$mail->SMTPAuth = true;
$mail->Port = PORT_EMAIL;
$mail->Username = USER_EMAIL;
$mail->Password = PASS_EMAIL;
$mail->From = USER_EMAIL;
$mail->AddReplyTo ( USER_EMAIL );
$mail->FromName = "RM TOTVS";
$mail->WordWrap = 50;
$mail->IsHTML ( true );
$mail->Subject = $assunto;
$mail->Body = $conteudo;

foreach($vetEmails["demissao"] as $email){
    $mail->AddAddress ($email);
}

if ($count > 0) {
    $mail->Send ();
}

$mail->SmtpClose ();

?>