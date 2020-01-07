<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Site Title   -->
<title>Unsubscribe</title>

<!-- Bootstrap css-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

</head>
<body>

<?php
include 'settings.php';

// name block
if (isset($_POST['name'])) {
	$safeinput = htmlentities($_POST['name']);
	if($safeinput == NULL){
		$name = 'Anonymous'; 								
	} else {
		$name = $safeinput;
	}
}

// email block
if (isset($_POST['email'])) {
	
	// first check if email already exits
	$email = $_POST['email'];
	$email_encrypt = openssl_encrypt($email, $ciphering, $encryption_key, $options, $encryption_iv); // encrypt email for searching
	$checkthis = strtolower($email_encrypt);		
	$email_matches = array();			
	$files = glob("subscribers/*.txt"); // Specify the file directory by extension (.txt)

	foreach($files as $file) { // Loop the files in the directory	
			
		$handle = @fopen($file, "r");
								
		if ($handle) {
																									
			while (!feof($handle)) {
				$buffer = fgets($handle);
				
				if(strpos(strtolower($buffer), $checkthis) !== FALSE) { // strtolower; search word not case sensitive	
													
					$email_matches[] = $file; // put all lines in array 	
					if (!empty($email_matches)) { //match found; email already exists
						echo '<div class="alert alert-danger"><b>'.openssl_decrypt($email_encrypt, $ciphering, $encryption_key, $options, $encryption_iv).'</b> already found in our mailinglist. Probably you already have subscribed!</div>';
						exit;
					}
				}
			}			
			fclose($handle);
		}		
	}
						
	$safeinput = htmlentities($_POST['email']);
	if($safeinput == NULL){
		echo 'Email required!';
		exit;
	} else {
		$email = $safeinput;
	}
	
	// Create txt file temporarely for checking later if url request really comes from confirm-link						
	$check_string = 'unconfirmed/' . base64_encode($email) . '.txt'; //name of the file is the same as encoded email	
	$h = fopen($check_string, 'w');
	
}

$url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// if no url string, request comes from subscribe form. First send confirm link via email
if ( parse_url($url, PHP_URL_QUERY) == NULL ) {
	
	
	// Send confirmation email for subscribing
	require 'phpmailer/class.phpmailer.php'; 		// load phpmailer						
	$mail = new PHPMailer;
	$mail->IsSMTP();								//Sets Mailer to send message using SMTP
	$mail->Host = $smtp_host;		                //Sets the SMTP hosts of your Email hosting, this for Godaddy
	$mail->Port = $smtp_port;						//Sets the default SMTP server port
	$mail->From = $from_email;						//Sets the From email address for the message
	$mail->FromName = $from_name;					//Sets the From name of the message
	$mail->IsHTML(true);							//Sets message type to HTML				
	$mail->Subject = "Newsletter";				    //Sets the Subject of the message
	$mail->AddAddress($email, $name);               //Adds a "To" address 
			
	try {	
		$confirm_text = 'You received this email because you have subscribed to our Newsletter.<br />Please confirm your email by clicking on the link below:<br />';
		$subscribe_urlpath = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";
		
		$mail->Body = $confirm_text.'<a href="'.$subscribe_urlpath.'?name='.urlencode(base64_encode($name)).'&email='.urlencode(base64_encode($email)).'">Confirm your email</a>';	
		$mail->Send();	
		$result = '<div class="alert alert-info">A confirmation link is sent to:<b> '.$email.'</b><br />Click on the link in the email to confirm your subscription!</div>';					
		echo $result;
	} catch (Exception $e) {
		$result = '<div class="alert alert-danger">Mailer Error (' . htmlspecialchars($email) . ') ' . $mail->ErrorInfo . '</div>';
		
	}
	
}

// check if there is a url string with name and email
if ( parse_url($url, PHP_URL_QUERY) != NULL ) { 

	// first check if email already exits
	$email = $_GET['email'];
	$email_decode = openssl_encrypt(base64_decode(urldecode($email)), $ciphering, $encryption_key, $options, $encryption_iv); // decode email from string
	$checkthis = strtolower($email_decode);		
	$email_matches = array();			
	$files = glob("subscribers/*.txt"); // Specify the file directory by extension (.txt)

	foreach($files as $file) { // Loop the files in the directory	{
			
		$handle = @fopen($file, "r");
								
		if ($handle) {
																									
			while (!feof($handle)) {
				$buffer = fgets($handle);
				
				if(strpos(strtolower($buffer), $checkthis) !== FALSE) { // strtolower; search word not case sensitive	
													
					$email_matches[] = $file; // put all lines in array 	
					if (!empty($email_matches)) { //match found; email already exists
						echo '<div class="alert alert-danger"><b>'.openssl_decrypt($email_decode, $ciphering, $encryption_key, $options, $encryption_iv).'</b> found in our mailinglist. Probably you already have confirmed!</div>';
						
						exit;
					}
				}
			}			
			fclose($handle);
		}		
	}
	
	// check if subscriber really send a request via the form
	$check_file = 'unconfirmed/'.$_GET['email'].'.txt'; 
	if( !file_exists($check_file) ) {
		echo '<div class="alert alert-danger">For subscribing to our mailinglist, fill in the form: <a href="form.php">Subscribe</a>';
		exit;
	}
	else {
		unlink($check_file);
	}

	// category block; default Offside
	$category = 'Offside';	

	// set a unique id as name of txt file
	$unique_id = 'id-'.uniqid();

	//get data from url string and decode
	$confirmed_name = $_GET['name'];
	$confirmed_email = $_GET['email'];
	$name_decode = urldecode(base64_decode($confirmed_name));	
	$email_decode = urldecode(base64_decode($confirmed_email));
	$token = md5($unique_id); 
	
	// put content in .txt file with linebreaks; unique_id first
	$input = $unique_id.PHP_EOL;
	$input .= $category.PHP_EOL;
	$input .= openssl_encrypt($name_decode, $ciphering, $encryption_key, $options, $encryption_iv).PHP_EOL;
	$input .= openssl_encrypt($email_decode, $ciphering, $encryption_key, $options, $encryption_iv).PHP_EOL;
	$input .= $token.PHP_EOL;

	// Write data to .txt file								
	$subscribe_file = 'subscribers/'.$unique_id . '.txt'; //name of the file is the same as unique_id

	$h = fopen($subscribe_file, 'w+');
	fwrite($h, html_entity_decode($input));
	fclose($h);
	
	// confirmation email to yourself when someone subscribed
	if($confirm_mail_to_you) {
		require 'phpmailer/class.phpmailer.php'; 		// load phpmailer						
		$mail = new PHPMailer;
		$mail->IsSMTP();								//Sets Mailer to send message using SMTP
		$mail->Host = $smtp_host;		                //Sets the SMTP hosts of your Email hosting, this for Godaddy
		$mail->Port = $smtp_port;						//Sets the default SMTP server port
		$mail->From = $from_email;						//Sets the From email address for the message
		$mail->FromName = $from_name;					//Sets the From name of the message
		$mail->IsHTML(true);							//Sets message type to HTML				
		$mail->Subject = "New Subscriber";				    //Sets the Subject of the message
		$mail->AddAddress($youremail, $yourname);               //Adds a "To" address 
		$mail->Body = '<b>'.$name_decode.'</b> with email: '.$email_decode.' has subscribed to your mailinglist!';	
		$mail->Send();	
	
	}

	echo '<div class="alert alert-success"><b>'.$email_decode.'</b> has been successfully added to our mailinglist!</div>';
	
}
?>
</div>
</body>
</html>