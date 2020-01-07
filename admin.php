<?php
include 'settings.php';
$dir = 'subscribers/';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				
	if(isset($_POST["checkbox_value"])) {
										
		$checkboxfiles[] = $_POST["checkbox_value"];
						
		// checkbox loop
		foreach ($checkboxfiles as $checkboxfile) {
			foreach(explode(',', $checkboxfile) as $checkbox_id) {
				$filename = 'subscribers/'.$checkbox_id.'.txt';
				
				$id = $_POST["file_id"];
				$category = $_POST["category"];					
				//get the file and read the values
				$filename = 'subscribers/'.$checkbox_id.'.txt';													
				$lines = file($filename, FILE_IGNORE_NEW_LINES); // filedata into array
				$file_id = $lines[0];               // file id
				$subscribers_category = $lines[1];	// category
				$subscribers_name = $lines[2];      // name (encrypted)
				$subscribers_email = $lines[3];     // email (encrypted)
				$subscribers_token = $lines[4];     // token 
				
				$button_value = $_POST["button_value"];
				if($button_value == 'delete') {
					// delete files
					unlink($filename);					
					$result = '<div class="alert alert-success">Selected items are deleted!</div>';
				}
				else {
					// change category
					$input = $file_id.PHP_EOL;
					$input .= $category.PHP_EOL; // new set category
					$input .= $subscribers_name.PHP_EOL;
					$input .= $subscribers_email.PHP_EOL;	
					$input .= $subscribers_token.PHP_EOL;
					
					// Write data to .txt file											
					$h = fopen($filename, 'w+');
					fwrite($h, html_entity_decode($input));
					fclose($h);
					
					$result =  '<div class="alert alert-success">Selected items moved to: <b>'.$category.'</b></div>';
		
				}
												
			}
		}
		echo $result;	
		exit;
	}
	
	// single item (no checkbox)
	if(isset($_POST["file_id"])) {
		
		$id = $_POST["file_id"];
		$category = $_POST["category"];			
		//get the file and read the values
		$filename = 'subscribers/'.$id.'.txt';													
		$lines = file($filename, FILE_IGNORE_NEW_LINES); // filedata into array
		$file_id = $lines[0];               // file id
		$subscribers_category = $lines[1];  // category
		$subscribers_name = $lines[2];      // name (encrypted)
		$subscribers_email = $lines[3];     // email (encrypted)
		$subscribers_token = $lines[4];     // token
				
		$button_value = $_POST["button_value"];
		if($button_value == 'delete') {
			// deleting file
			unlink($filename);
			echo '<div class="alert alert-success"><b>'.openssl_decrypt ($subscribers_email, $ciphering,  $encryption_key, $options, $encryption_iv).'</b> deleted!</div>';
		}
		else {
			// changing category
			$input = $file_id.PHP_EOL;
			$input .= $category.PHP_EOL; // new set category
			$input .= $subscribers_name.PHP_EOL;
			$input .= $subscribers_email.PHP_EOL;	
			$input .= $subscribers_token.PHP_EOL;
			
			// Write data to .txt file											
			$h = fopen($filename, 'w+');
			fwrite($h, html_entity_decode($input));
			fclose($h);
			
			echo '<div class="alert alert-success"><b>'.openssl_decrypt ($subscribers_email, $ciphering,  $encryption_key, $options, $encryption_iv).'</b> moved to <b>'.$category.'</b></div>';
		}
							
		exit;
	}
	
}


/* ADD SUBSCRIBER MANUALLY (ADMIN ONLY) */
$addname = $_GET['name'];
$addemail = $_GET['email'];

if ( isset($addname) && isset($addemail) )  {
		
	
	// first check if email already exits
	$email = $_POST['email'];
	$email_encrypt = openssl_encrypt($addemail, $ciphering, $encryption_key, $options, $encryption_iv); // encrypt email for searching
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
						$email_exists = true;
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
	
	if(!$email_exists) {
		$h = fopen($subscribe_file, 'w+');	 
		fwrite($h, html_entity_decode($input));
		
		fclose($h);

		$result = '<div class="alert alert-success"><b>'.$addemail.'</b> has been successfully added to the mailinglist!</div>';

		header('Location: admin.php?search='.$addemail); // redirects to the added subscriber
		exit;
	}
	else {
		$result = '<div class="alert alert-danger"><b>'.openssl_decrypt($email_encrypt, $ciphering, $encryption_key, $options, $encryption_iv).'</b> already exists!</div>';
						
	}	
}


/* FILTER BY CATEGORY */
$filter_category = $_GET['filter_category'];
if ( isset($filter_category) )  {
			
	$search_category = strtolower($filter_category);	
	$category_matches = array();
		
	$files = glob("subscribers/*.txt"); // Specify the file directory by extension (.txt)

	foreach($files as $file) { // Loop the files in the directory	{
			
		$handle = @fopen($file, "r");
								
		if ($handle) {
			
			// for sorting files by name
			$lines = file($file);			
			$grab_email = strtolower($lines[2]); 
																	
			while (!feof($handle)) {
				$buffer = fgets($handle);
				
				if(strpos(strtolower($buffer), $search_category) !== FALSE) // strtolower; search word not case sensitive	
													
					$category_matches[$file] = $grab_email; // put all lines in array 
					
			}
			
			fclose($handle);
		}
	}
		
}
asort($category_matches);


/* SEARCH */
$search = openssl_encrypt($_GET['search'], $ciphering, $encryption_key, $options, $encryption_iv); // get string from url for search and encrypt for searching
if (isset($search)) {
	$searchthis = strtolower($search);	
	$email_matches = array();
		
	$files = glob("subscribers/*.txt"); // Specify the file directory by extension (.txt)

	foreach($files as $file) { // Loop the files in the directory	{
			
		$handle = @fopen($file, "r");
								
		if ($handle) {
			
			// for sorting files by name
			$lines = file($file);			
			$grab_email = strtolower($lines[2]); // 
																	
			while (!feof($handle)) {
				$buffer = fgets($handle);
				
				if(strpos(strtolower($buffer), $searchthis) !== FALSE) // strtolower; search word not case sensitive	
													
					$email_matches[$file] = $grab_email; // put all lines in array 	
					
			}
			
			fclose($handle);
		}
	}
			
}
asort($email_matches);


/* SENDING EMAIL */
$result = '';
$name = '';
$email = '';
$subject = '';
$message = '';

function clean_text($string)
{
	$string = trim($string);
	$string = stripslashes($string);
	$string = htmlspecialchars($string);
	return $string;
}

if(isset($_POST["send_newsletter"])) {
				
	$message = clean_text($_POST["message"]);
	
	if($result == '') {
			
	
		 //Grab all the files from all the categories		
		if ($dh = opendir($dir)) {
			while(($file = readdir($dh))!== false){
				if ($file != "." && $file != "..") { // This line strips out . & ..										
					$all_subscribers[] = $file;   // put all files in array 
				}
			}			
		}
		closedir($dh);			
		
		// Grab all the emails from the choosen category
		$recipients_category = $_POST['recipients_category']; // get value from choosen category
		if ( isset($recipients_category) )  {
							
			$search_category = strtolower($recipients_category);	
			$category_matches = array();
				
			$files = glob("subscribers/*.txt"); // Specify the file directory by extension (.txt)

			foreach($files as $file) { // Loop the files in the directory	{
					
				$handle = @fopen($file, "r");
										
				if ($handle) {
																			
					while (!feof($handle)) {
						$buffer = fgets($handle);
						
						if(strpos(strtolower($buffer), $search_category) !== FALSE) { // strtolower; search word not case sensitive	
															
							$category_matches[] = $file; // put all lines in array indexed by file name	$category_matches[] = $file; 
						}							
					}					
					fclose($handle);
				}
			}				
		}

		
		require 'phpmailer/class.phpmailer.php'; // load phpmailer						
		$mail = new PHPMailer;
		$mail->IsSMTP();								//Sets Mailer to send message using SMTP
		$mail->Host = $smtp_host;		                //Sets the SMTP hosts of your Email hosting, this for Godaddy
		$mail->Port = $smtp_port;						//Sets the default SMTP server port
		$mail->From = $from_email;						//Sets the From email address for the message
		$mail->FromName = $from_name;					//Sets the From name of the message
		$mail->Sender = $bounce;                        // Sets the sender
		$mail->IsHTML(true);							//Sets message type to HTML				
		$mail->Subject = "Newsletter";				    //Sets the Subject of the message
		
		/* SEND TO ALL CATEGORIES */
		if($recipients_category == 'All-categories') { 
									
			foreach($all_subscribers as $file) { 	
				// open and prepare files
				$subscribers_files = 'subscribers/'.$file;			
				// get data out of txt file		
				$lines = file($subscribers_files, FILE_IGNORE_NEW_LINES); // set lines from all files into array								
								
				 if ($lines[1] != 'Offside') { // skip category offside
				 
					include 'includes/mail.php';
					
				}								
							
			}			
		}
		else {
			/* SEND TO SINGLE CATEGORIE */
			foreach($category_matches as $file) { 
				// get data out of txt file		
				$lines = file($file, FILE_IGNORE_NEW_LINES); // set lines from matched file into array
				
				include 'includes/mail.php';
				
			}
		}
									
		$name = '';
		$email = '';
		$subject = '';
		$message = '';
	}
}

?>
<!DOCTYPE html>
<html>
<head>
<title>Simple mailing-list</title>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>	
<!-- API key for TinyMCE -->
<script src="https://cdn.tiny.cloud/1/cu9iuv1soi8lkx4dfa0qp167qpr7pw81y9rj9n42dvtj1mch/tinymce/5/tinymce.min.js"></script> 

</head>
<body>
<br />
<div class="container">

	<div class="row">
	
		<div class="col-md-9 col-lg-9">
		
			<div class="result"></div> <!-- ajax response -->
		
		
			
		
			<div class="subscribers-list">
						
			<?php
			
			/* OUTPUT THE SUBSCRIBERS */
			
			//  Output list of all subscribers
			$all_subscribers = $_GET['all_subscribers'];
			if( isset($all_subscribers) ) {
				$all_files = glob("subscribers/*.txt"); // read all files
				$total_subscribers = count($all_files); // count total files
				?>
				<a href="admin.php">Send Newsletter</a>
				<h5>All subscribers&nbsp;&nbsp;(<?php echo $total_subscribers; ?>)</h5><br />
													
				<?php include 'includes/checkboxactions.php'; ?>
				
				<table class="table">
				<thead><tr><th><input type="checkbox" class="selectall" /></th><th>Name</th><th>Email</th><th>Category</th><th>Update Category</th><th>Delete</th></tr></thead>
				
				<?php					
				foreach($all_files as $file) { // sort each entry by email
				
					$lines = file($file, FILE_IGNORE_NEW_LINES); // filedata into array
					
					include 'includes/output.php';

				}
				?></table><?php
			}
			
			//  Output list of search matches
			$search = $_GET['search'];
			if( isset($search) ) {
				$total_matches = count($email_matches); // count number of matches
				?>
				<a href="admin.php">Send Newsletter</a>
				<h5>Search results for:<b>&nbsp;<?php echo $search; ?></b>&nbsp;&nbsp;(<?php echo $total_matches; ?>)</h5><br />
							
				<?php include 'includes/checkboxactions.php'; ?>
				
				<table class="table">
				<thead><tr><th><input type="checkbox" class="selectall" /></th><th>Name</th><th>Email</th><th>Category</th><th>Update Category</th><th>Delete</th></tr></thead>
				
				<?php
				foreach($email_matches as $file => $email) { // sort each entry by email
							
					// get data out of txt file		
					$lines = file($file, FILE_IGNORE_NEW_LINES); // set lines from matched file into array
					
					include 'includes/output.php';
					
				}
				?></table><?php

			}
			//  Output list filtered by category
			if( isset($filter_category) ) {
				$total_matches = count($category_matches); // count number of matches
				?>
				<a href="admin.php">Send Newsletter</a>
				<h5>Results for category:<b class="category">&nbsp;<?php echo $filter_category; ?></b>&nbsp;&nbsp;(<?php echo $total_matches; ?>)</h5><br />					
				
				<?php include 'includes/checkboxactions.php'; ?>
				
				<table class="table">
				<thead><tr><th><input type="checkbox" class="selectall" /></th><th>Name</th><th>Email</th><th>Category</th><th>Update Category</th><th>Delete</th></tr></thead>
				
				<?php
				foreach($category_matches as $file => $email) { // sort each entry by email
							
					// get data out of txt file		
					$lines = file($file, FILE_IGNORE_NEW_LINES); // set lines from matched file into array
					
					include 'includes/output.php';
					
				}
				?></table><?php

			}
			?>
								
			</div> <!-- end subscribers list -->			
			
		
			
			<?php 			
			if($_SERVER['QUERY_STRING'] == NULL) { // if no query string, show email-form
			?>
				<!-- Form sending newsletter -->
				<h4>Sending newsletter</h4>
				
				<?php echo '<br />'.$result; ?>
				<form method="post" action='<?php $_SERVER['PHP_SELF']; ?>'>
				
					<div class="input-group mb-3">	
						<div class="input-group-prepend">
							<span class="input-group-text"><b>To:</b></span>
						</div>					
						<select class="category_selectbox form-control" name="recipients_category">							
							<option value="All-categories">All-categories</option>
							<?php 
								$arr_length = count($all_categories); // count the number of categories
								for ($x = 0; $x < $arr_length; $x++) {
							?>
							<option value="<?php echo $all_categories[$x]; ?>"><?php echo $all_categories[$x]; ?></option>
							<?php 
							} 
							?>									 								
						</select>
						
					</div>	
				
					<div class="form-group">
						<textarea name="message" class="tinymce form-control" placeholder="Enter Message"><?php echo $message; ?></textarea>
					</div>
					<button class="send_newsletter btn btn-primary" name="send_newsletter" type="submit">Send newsletter</button>
				</form>
				<br /><br />
			<?php 
				}
					
			?>
		</div>	
		
		<div class="col-md-3 col-lg-3">
			<h4>Sidecolumn</h4>
			<a href="admin.php?all_subscribers">All Subscribers</a>
			<br /><br />
			
			<h5 class="page-header">Filter by category</h5>				
			<!-- filter category -->
			<form class="search-form form-inline" action="admin.php" method="GET">
				<div class="input-group mb-3">					
					<select class="category_selectbox form-control" name="filter_category">					
						<?php 
							$arr_length = count($all_categories); // count the number of categories
							for ($x = 0; $x < $arr_length; $x++) {
						?>
						<option value="<?php echo $all_categories[$x]; ?>"><?php echo $all_categories[$x]; ?></option>
						<?php 
						} 
						?>
						<option class="text-danger font-italic" value="Offside">Offside</option>						
					</select>
					<div class="input-group-append">		
						<button class="btn btn-primary" type="submit">Filter</button>
					</div>	
				</div>							
			</form>
			<br />
			<h5 class="page-header">Search for name or email</h5>				
			<!-- search form -->
			<form class="search-form form-inline" action="admin.php" method="GET">	
				<div class="input-group mb-3">
					<input class="form-control" type="text" name="search" placeholder="Search for..." />
					<div class="input-group-append">				
						<button class="btn btn-primary" type="submit">Search</button>
					</div>
				</div>							
			</form>
			<br />
			<!-- add subscriber-->
			<h5 class="page-header">Add a subscriber</h5>
			<form id="addsubscriber" method="POST">	
				<div class="control-group form-group">
					<div class="controls">
						<input class="form-control addname" name="addname" type="text" placeholder="Name">
					</div>
				</div>
				<div class="control-group form-group">
					<div class="controls">
						<input class="form-control addemail" name="addemail" type="email" placeholder="Email" required>
					</div>
				</div>			
				<button type="submit" name="add-subscriber" class="btn btn-primary">Add Subscriber</button>									
			</form>				
			
		</div>
		
	</div>
</div>

<!-- tinymce -->
<script>
tinyMCE.init({
	selector : ".tinymce",
	plugins: "emoticons link image code autosave template preview",
	autosave_ask_before_unload: false,
	autosave_interval: "20s",	
	menubar: true,
	toolbar: 'undo redo | bold italic underline | fontsizeselect | link | emoticons | image | code | template | preview | restoredraft',  
	templates: [
		{title: 'Choose template' , description: ''},
		/* LIST OF TEMPLATES */
		{title: 'Template Cerberus', description: 'Loaded: Template Cerberus', url: 'templates/cerberus.html'},
		// Load more templates
		/*{title: 'Template 1', description: 'Loaded: Template 1', url: 'templates/template1.html'}, 
		{title: 'Template 2', description: 'Loaded: Template 2', url: 'templates/template2.html'},
		{title: 'Template 3', description: 'Loaded: Template 3', url: 'templates/template3.html'},
		{title: 'Template 4', description: 'Loaded: Template 4', url: 'templates/template4.html'},*/
	],	
	

	height: 600,
	force_br_newlines : true,
	force_p_newlines : false,
	forced_root_block : '',
	
	relative_urls: false,
	remove_script_host : false,
	convert_urls : true,
	images_upload_url : 'upload.php',	
	automatic_uploads : false,
	
	image_class_list: [
    {title: 'Responsive', value: 'img-responsive'}
    ],
	
	images_upload_handler : function(blobInfo, success, failure) {
		var xhr, formData;

		xhr = new XMLHttpRequest();
		xhr.withCredentials = false;
		xhr.open('POST', 'upload.php');

		xhr.onload = function() {
			var json;

			if (xhr.status != 200) {
				failure('HTTP Error: ' + xhr.status);
				return;
			}

			json = JSON.parse(xhr.responseText);

			if (!json || typeof json.file_path != 'string') {
				failure('Invalid JSON: ' + xhr.responseText);
				return;
			}

			success(json.file_path);
		};

		formData = new FormData();
		formData.append('file', blobInfo.blob(), blobInfo.filename());

		xhr.send(formData);
	},
	
    mobile: {
		theme: 'silver',
		plugins: 'emoticons link',
		toolbar: 'undo redo | bold italic underline | fontsizeselect | link | emoticons | image | code'
	}					
});


$( document ).ready(function() {

	// checkbox select all 
	$(document).on('change' , '.selectall' , function() {			 
		$('input:checkbox').not(this).prop('checked', this.checked);
		$('.checkbox_actions').toggleClass('d-none', $('.checkbox:checked').length < 1);
		$('.checkbox_actions').toggleClass('show', $('.checkbox:checked').length > 0);			 
	 });
	
	// show/hide action buttons for checkboxes
	$(document).on('change','.checkbox',function() {
	  $('.checkbox_actions').toggleClass('d-none', $('.checkbox:checked').length < 1);
	  $('.checkbox_actions').toggleClass('show', $('.checkbox:checked').length > 0);
	  
	});	

	// select all checkboxes
	$('.selectall').click(function() {
		if ($(this).is(':checked')) {
			$('input:checkbox').prop('checked', true);
		} else {
			$('input:checkbox').prop('checked', false);
		}
	});
	
	// red color & italic Offside category
	$('.category:contains("Offside")').closest('.category').addClass('text-danger font-italic'); 
			
	// ajax for checkboxe values 
	$(document).on('click' , '.submit-checkbox' , function() { 
		var checkbox_value = [];
		var file_id = $(this).closest("tr").find("input[name='file_id']").val();
		var category = $(this).closest("tr").find("#category option:selected").val();
		var button_value = $(this).prev().val();		
		
		$('.checkbox').each(function() {
			if ($(this).is(":checked")) {
				checkbox_value.push($(this).val());
			}
		});
		checkbox_value = checkbox_value.toString();
		$.ajax({
			url: "admin.php",
			method: "POST",
			data: {
				checkbox_value: checkbox_value,
				file_id : file_id,
				category : category,
				button_value : button_value				
			},
			success: function(data) {
				$('.result').html(data);
				$('.checkbox_actions').removeClass('show');
				$('.checkbox_actions').addClass('d-none');
				$('input:checkbox').prop('checked', false);
				$(".subscribers-list").load(location.href + " .subscribers-list"); // reload subscribers-list div				
			}
			
		});
	});
	
	// ajax for single value 
	$(document).on('click' , '.submit-single' , function() { 
				
		var file_id = $(this).closest("tr").find("input[name='file_id']").val();
		var category = $(this).closest("tr").find("#category option:selected").val();
		var button_value = $(this).prev().val();
								
		$.ajax({
			url: "admin.php",
			method: "POST",
			data: {				
				file_id : file_id,
				category : category,
				button_value : button_value				
			},
			success: function(data) {
				$('.result').html(data);
				$('.checkbox_actions').removeClass('show');
				$('.checkbox_actions').addClass('d-none');
				$('input:checkbox').prop('checked', false);				
				$(".subscribers-list").load(location.href + " .subscribers-list"); // reload subscribers-list div				
			}
			
		});
	});
	
	
	// Add subscriber manually	
	$('#addsubscriber').on('submit', function(e) {
		e.preventDefault();		
		var addname = $(".addname").val();
		var addemail = $(".addemail").val();		
		
		$.ajax({
			url: 'addsubscriber.php',
			method: 'POST',			
			data: {
				addname : addname,
				addemail : addemail				
			},			
			success: function(data){	
				$('.result').html(data);
				$(".subscribers-list").load(location.href + " .subscribers-list"); // reload subscribers-list div
								
			}
		});	
		$('#addsubscriber')[0].reset();
		return false;
	});
	
								
}); // document ready

</script>


</body>
</html>





