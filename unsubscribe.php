<?php
include 'settings.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			
	$id = $_POST['id'];
	$email = $_POST['email'];		
	$token = $_POST['token'];
	
	// decrypt email
	$decrypted_decode_email = openssl_decrypt ($email , $ciphering,  $encryption_key, $options, $encryption_iv); 
	
	// decode the id, email and token 
	$id_decode = urldecode($id);		
	$email_decode = urldecode($decrypted_decode_email);
	$token_decode = urldecode($token);
													
	$filename = 'subscribers/'.$id_decode.'.txt';		
	// get data out of txt file		
	$lines = file($filename, FILE_IGNORE_NEW_LINES); // set lines from all files into array
	
	// id & token which are stored in the file; we check them later if they are similar as the $_GET['id'] and $_GET['token']
	$check_id = $lines[0];	
	$check_token = $lines[4];
		
	// delete subscribers entry
	if(file_exists($filename)) { 
	
		if( $check_id == $id_decode && $check_token == $token_decode) { // Check if identity and token of the file match			
			unlink($filename);	
			echo '<div class="alert alert-success"><b>'.$email_decode.'</b> is successfully removed from our mailinglist!</div>';	
		}
		else {
			echo '<div class="alert alert-danger">Identity and token do not match</div>';
		}				
	}
	else {
		echo '<div class="alert alert-danger">Email not found or you already have unsubscribed from our mailinglist!</div>';
	}
		
	exit;
}	

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Site Title   -->
<title>Unsubscribe</title>

<!-- jQuery core -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<!-- Bootstrap css-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">

</head>
<body>
<br />

<h5>Unsubscribe</h5>

<div class="result"></div> <!-- ajax response -->
You are about to unsubscribe from our mailing list.<br />
Do you really want to unsubscribe?<br /><br />

<form name ="unsubscribe" id="unsubscribe" method="POST" role="form">			
	<input name="id" type="hidden" value="<?php echo $_GET['id']; ?>" />
	<input name="email" type="hidden" value="<?php echo $_GET['email']; ?>" />	
	<input name="token" type="hidden" value="<?php echo $_GET['token']; ?>" />
			
	<button type="submit" name="submit" class="btn btn-danger">Yes, unsubscribe me!</button>									
</form>

	
<script>     
$(document).ready(function(){		
	$('#unsubscribe').on('submit', function(e){
		e.preventDefault();
		
		var formData = new FormData(this);
							
		$.ajax({
			url: '',
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			success: function(data){
				$('.result').show();	
				$('.result').html(data);
								
			}
		});
	
		$('#unsubscribe')[0].reset();
		return false;
	});
				
});
</script>

</body>
</html>
