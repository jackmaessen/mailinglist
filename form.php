<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Site Title   -->
<title>Submit-form</title>
<!-- some basic styling-->
<link rel="stylesheet" href="css/style.css">
<!-- Bootstrap css-->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<!-- jQuery core -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>


</head>
<body>
<br />

	<h5>Subscribe to our mailinglist</h5>
	<div class="result"></div> <!-- ajax response -->

	<form name ="subscriber" id="subscriber" action="" method="POST" role="form">	
		<div class="control-group form-group">
			<div class="controls">
				<input class="form-control name" name="name" type="text" placeholder="Name">
			</div>
		</div>
		<div class="control-group form-group">
			<div class="controls">
				<input class="form-control email" name="email" type="email" placeholder="Email" required>
			</div>
		</div>			
		<button type="submit" id="cf-submit" name="submit" class="btn btn-primary">Subscribe</button>									
	</form>

		
<script>     
$(document).ready(function(){
		
	$('#subscriber').on('submit', function(e){
		e.preventDefault();
		
		var formData = new FormData(this);
		
					
		$.ajax({
			url: 'subscribe.php',
			data: formData,
			processData: false,
			contentType: false,
			type: 'POST',
			success: function(data){
				$('.result').show();	
				$('.result').html(data);
								
			}
		});
	
		$('#subscriber')[0].reset();
		return false;
	});
				
});
</script>

</body>
</html>