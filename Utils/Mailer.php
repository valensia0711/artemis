<?php
require_once(dirname(__FILE__).'/PHPMailer/class.phpmailer.php');
require_once(dirname(__FILE__).'/PHPMailer/class.smtp.php');
require_once(dirname(__FILE__).'/PHPMailer/language/phpmailer.lang-es.php');

function sendMail($subject, $message, $targets){
	$from = "admin@nussucommit.com";
	$from_name = "NUSSU commIT";

	$mail = new PHPMailer(true);
	$mail->IsSMTP();
	$mail->Host = "smtp.gmail.com";
	$mail->SMTPSecure = 'ssl';
	$mail->Port = 465;
	$mail->SMTPAuth = true;
	$mail->Username = "dutygrab@gmail.com";
	$mail->Password = "teamnuschess";
	$mail->FromName = $from_name;
	$mail->From = $from;

	for($i = 0; $i < count($targets); $i++){
		$mail->AddAddress($targets[$i]['email'], $targets[$i]['name']);
	}
	
	$mail->Subject = $subject;
	$mail->Body = $message;
	try{
		$result = $mail->Send();
	} catch (Exception $e){
		file_put_contents("log1.txt",date("r").' '.$e->getMessage().PHP_EOL,FILE_APPEND);
	}	
}

?>