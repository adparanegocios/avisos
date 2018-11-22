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
    CONVERT(VARCHAR(10),P.DATAINICIO,103) AS [INICIO],
    CONVERT(VARCHAR(10),P.DATAFIM,103) AS [FIM],
    CONVERT(VARCHAR(10),DATEADD(DAY,1,P.DATAFIM),103) AS [RETORNO] 
FROM PFUFERIASPER P (NOLOCK)
INNER JOIN GCOLIGADA E (NOLOCK) ON (E.CODCOLIGADA = P.CODCOLIGADA)
INNER JOIN PFUNC F (NOLOCK) ON (F.CHAPA = P.CHAPA AND F.CODCOLIGADA = P.CODCOLIGADA)
INNER JOIN PSECAO S (NOLOCK) ON (S.CODIGO = F.CODSECAO AND S.CODCOLIGADA = F.CODCOLIGADA)
INNER JOIN PFUNCAO C (NOLOCK) ON (C.CODIGO = F.CODFUNCAO AND C.CODCOLIGADA = F.CODCOLIGADA)
INNER JOIN PFUFERIASRECIBO R (NOLOCK) ON (R.CHAPA = P.CHAPA AND R.FIMPERAQUIS = P.FIMPERAQUIS AND R.DATAPAGTO = P.DATAPAGTO AND R.CODCOLIGADA = P.CODCOLIGADA)
WHERE
    P.CODCOLIGADA IN (1, 2, 3, 4, 5, 6, 8) AND
    CONVERT(DATETIME,CONVERT(VARCHAR,P.DATAINICIO,103),103) BETWEEN CONVERT(DATETIME,CONVERT(VARCHAR,DATEADD(MM, DATEDIFF(MM,0,GETDATE()) + 1, 0),103),103) AND CONVERT(DATETIME,CONVERT(VARCHAR,DATEADD(MM, DATEDIFF(MM,0,GETDATE()) + 2, -1),103),103)
ORDER BY [CODIGO], [NOME]
";

$rs = $conexao->prepare($sql);
$rs->execute();

$data_incio = date('d/m/Y',mktime(0, 0, 0, date('m')+1 , 1 , date('Y')));
$data_fim = date('d/m/Y',mktime(23, 59, 59, date('m')+2, date('d')-date('j'), date('Y')));
$assunto = utf8_decode("[TOTVS] Escala de Férias - Período: $data_incio à $data_fim");
$count = 0;

$conteudo = "
<html>
	<head>
		<title>Avisos</title>
	</head>
	<body>
		<div style='text-align: center;'>
        <strong>ESCALA DE F&Eacute;RIAS - PER&Iacute;ODO: $data_incio &Agrave; $data_fim</strong>
        </div>
		<div style='text-align: justify;'>
			&nbsp;</div>
		<div style='text-align: justify;'>
			<table align='center' border='1' cellpadding='1' cellspacing='1'>
				<caption>
					<span style='font-size:20px;'><em><strong>Colaboradores</strong></em></span></caption>
				<thead>
					<tr>
						<th scope='col'>
							EMPRESA</th>
						<th scope='col'>
							MATR&Iacute;CULA</th>
						<th scope='col'>
							NOME</th>
						<th scope='col'>
							SE&Ccedil;&Atilde;O</th>
						<th scope='col'>
							FUN&Ccedil;&Atilde;O</th>
						<th scope='col'>
							<span style='color:#ff0000;'>IN&Iacute;CIO</span></th>
						<th scope='col'>
							FIM</th>
						<th scope='col'>
							<span style='color:#ff0000;'>RETORNO</span></th>
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
    $conteudo .= "
    <tr>
		<td>$empresa</td>
		<td>$matricula</td>
		<td>$nome</td>
		<td>$secao</td>
		<td>$funcao</td>
		<td><span style='color:#ff0000;'>{$row->INICIO}</span></td>
		<td>{$row->FIM}</td>
		<td><span style='color:#ff0000;'>{$row->RETORNO}</span></td>
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

foreach($vetEmails["ferias"] as $email){
    $mail->AddAddress ($email);
}

if ($count > 0) {
    $mail->Send ();
}

$mail->SmtpClose ();

?>