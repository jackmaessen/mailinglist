<?php
include 'settings.php';

$addname = $_POST['addname'];
$addemail = $_POST['addemail'];

if ( isset($addname) && isset($addemail) )  {
		
	// first check if email already exits	
	$addemail_encrypt = openssl_encrypt($addemail, $ciphering, $encryption_key, $options, $encryption_iv); // encrypt email for searching
	$checkthis = strtolower($addemail_encrypt);		
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
						echo '<div class="alert alert-danger"><b>'.openssl_decrypt($addemail_encrypt, $ciphering, $encryption_key, $options, $encryption_iv).'</b> already exists!</div>';
						exit;
					}
				}
			}			
			fclose($handle);
		}		
	}
	// category block; default Offside
	$category = 'Offside';	
	
	// set a unique id as name of txt file
	$unique_id = 'id-'.uniqid();
	
	$token = md5($unique_id); 

	// put content in .txt file with linebreaks; unique_id first
	$input = $unique_id.PHP_EOL;
	$input .= $category.PHP_EOL;
	$input .= openssl_encrypt($addname, $ciphering, $encryption_key, $options, $encryption_iv).PHP_EOL;
	$input .= openssl_encrypt($addemail, $ciphering, $encryption_key, $options, $encryption_iv).PHP_EOL;
	$input .= $token.PHP_EOL;

	// Write data to .txt file								
	$subscribe_file = 'subscribers/'.$unique_id . '.txt'; //name of the file is the same as unique_id

	$h = fopen($subscribe_file, 'w+');
	fwrite($h, html_entity_decode($input));
	fclose($h);

	echo '<div class="alert alert-success"><b>'.$addemail.'</b> has been successfully added to the mailinglist!</div>';
}	
?>
