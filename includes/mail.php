<?php
include 'settings.php';

$recipients_id = $lines[0]; //  id of recipients
$recipients_name = $lines[2]; //  name of recipients (encrypted)
$recipients_email = $lines[3]; //  email of the recipients (encrypted)
$recipients_token = $lines[4]; //  token of the recipients

$decrypted_recipients_name = openssl_decrypt ($recipients_name, $ciphering,  $encryption_key, $options, $encryption_iv); 
$decrypted_recipients_email = openssl_decrypt ($recipients_email, $ciphering,  $encryption_key, $options, $encryption_iv); 

$mail->AddAddress($decrypted_recipients_email, $decrypted_recipients_name);		//Adds a "To" address

// send email with unsubscribe link
try {
	$unsubscribe_urlpath = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/$relative_unsubscribe_urlpath";	
	$mail->Body = $_POST["message"].'<br /><br />'.'<a href="'.$unsubscribe_urlpath.'?id='.urlencode($recipients_id).'&email='.urlencode($recipients_email).'&token='.urlencode($recipients_token).'">Unsubscribe</a>';	
	$mail->Send();	
	$result = '<div class="alert alert-success">Newsletter sent to subscribers of:<b> '.$recipients_category.'</b></div>';					
	
} catch (Exception $e) {
	$result = '<div class="alert alert-danger">Mailer Error (' . htmlspecialchars($decrypted_recipients_email) . ') ' . $mail->ErrorInfo . '</div>';
	$mail->smtp->reset(); // reset SMTP connection
}
				
$mail->clearAddresses(); // Clear all addresses for the next iteration
