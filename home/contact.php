<?php

require_once('PHPMailer-6.6.0/src/PHPMailer.php');
require_once('PHPMailer-6.6.0/src/Exception.php');
require_once('PHPMailer-6.6.0/src/SMTP.php');



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
ini_set('display_errors', true);

// set initial header to forbidden
header($_SERVER["SERVER_PROTOCOL"]." 503  Forbidden"); 
// check _POST fields

$max_name_size=60;
$max_company_size=60;
$max_email_size=60;
$max_body_size=2048;

if (!isset($_POST["name"]) || !isset($_POST["company"]) || !isset($_POST["email"]) || !isset($_POST["body"]))
{
	header($_SERVER["SERVER_PROTOCOL"]." 402 Payload Required");
	echo "Unfortunately no message was sent";	
	exit(0);
}

if (strlen($_POST["name"]) > $max_name_size ||
	strlen($_POST["company"]) > $max_company_size ||
	strlen($_POST["email"]) > $max_email_size ||
	strlen($_POST["body"]) > $max_body_size )
{
	header($_SERVER["SERVER_PROTOCOL"]." 413 Payload Too Large");
	echo "inputfields contain too much data";
	exit(0); 
}

$body_contents = "message sent by: " . $_POST["name"] . " from " . $_POST["company"] . "\r\n\r\n";
$body_contents .= "e-mail: " . $_POST["email"] . "\r\n\r\n" . "\r\n\r\n";
$body_contents .= "message: \r\n\r\n" . $_POST["body"];

if (extension_loaded('openssl')) 
{
  //  echo 'openssl extension loaded.';
}

$mail = new PHPMailer(true);
// testing code
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

// end testing code
$mail->CharSet =  "utf-8";
$mail->IsSMTP();
$mail->SMTPAutoTLS = false;
$mail->SMTPAuth = true;                  
$mail->Username='contact@myrtherijk.nl';
$mail->Password = "";
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->SMTPOptions = [
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
    ];
$mail->Host = "smtp.strato.com";

// set the SMTP port for the GMAIL server
$mail->Port = "587";
// $mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->From='contactform@myrtherijk.nl';
$mail->FromName='contact form';
$mail->AddAddress('mjrijk@hotmail.com');
$mail->Subject  =  'contact form myrtherijk.nl by :' . $_POST['name'] . " " . $_POST['company'];
$mail->IsHTML(true);
$mail->Body    = $body_contents;
// *************************** The server hangs right here ***********************
if($mail->Send())
{
	header($_SERVER["SERVER_PROTOCOL"]." 200  OK"); 
    echo "Your message has been sent, thank you!";
}
else 
{
	header($_SERVER["SERVER_PROTOCOL"]." 417 Expectation Failed"); 
    echo "Unfortunately no message was sent";
}
 

?>
