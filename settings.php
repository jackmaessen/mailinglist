<?php
// EDIT SETTINGS 

// category settings
$all_categories = array( // edit and add name of the categories to your own needs 	
	'Friends',
	'Family',
	'Collegas',
	'Club',
	'Other1',
	'Other2',
	'Other3'	
);
// email settings
$from_name = 'Newsletter'; // Set the From name of the message
$from_email = 'noreply@newsletter.org'; // Set the From email address for the message
$smtp_host = 'localhost'; // Set the SMTP hosts of your Email hosting, this for Godaddy
$smtp_port = '25'; // Set the default SMTP server port
$bounce = 'jonhdoe@email.org'; // set your email address here if you want a bounce ( email to someone could not be delivered)

// mail to yourself if someone has subscribed
$confirm_mail_to_you =  true; // 'true' or 'false'. Set to 'true' if you want a confirmation someone has subscribed. Else, set to 'false' 
$youremail = 'jonhdoe@email.org'; // if above set to 'true', fill in your email-address
$yourname = 'John'; // if above set to 'true', fill in your name

// relative path to file unsubscribe.php
$relative_unsubscribe_urlpath = 'unsubscribe.php'; // default in webroot. Else fill in path to subscribe.php; e.g. 'subfolder/unsubscribe.php';

// EDIT BELOW ONLY IF YOU KNOW WHAT YOU ARE DOING
// encrypt name and email 
$ciphering = "AES-128-CTR";   
$iv_length = openssl_cipher_iv_length($ciphering); 
$options = 0;  
$encryption_iv = '1234567891011121';  
$encryption_key = "k8GDGAN&9n1c0r2pt"; 
?>