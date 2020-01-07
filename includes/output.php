<?php

$file_id = $lines[0];               // file id
$subscribers_category = $lines[1];  // category
$subscribers_name = $lines[2];      // name (encrypted)
$subscribers_email = $lines[3];     // email (encrypted)
$subscribers_token = $lines[4];     // token

$decrypted_subscribers_name = openssl_decrypt ($subscribers_name, $ciphering,  $encryption_key, $options, $encryption_iv); 
$decrypted_subscribers_email = openssl_decrypt ($subscribers_email, $ciphering,  $encryption_key, $options, $encryption_iv);
?>


<tr>
	<input type="hidden" name="file_id" value="<?php echo $file_id; ?>" /> <!-- file id which belongs to each row -->
	<td><input type="checkbox" class="checkbox" value="<?php echo $file_id; ?>" /></td>
	
	<th><?php echo $decrypted_subscribers_name; ?></th>
	<td><?php echo $decrypted_subscribers_email; ?></td>
	<td class="category"><?php echo $subscribers_category; ?></td>
	<td>
		<!-- Category button -->					
			<div class="input-group input-group-sm">
				<select class="selectbox form-control" class="category" id="category" name="set_category">												
					<?php 						
						
						$arr_length = count($all_categories); // count the number of categories							
						// show all categories														
						for ($x = 0; $x < $arr_length; $x++) {
					?>
					<option value="<?php echo $all_categories[$x]; ?>"><?php echo $all_categories[$x]; ?></option> 
					<?php 
					} 
					?>	
					<option class="text-danger font-italic" value="Offside">Offside</option> 																					
				</select>
				<input type="hidden" class="name" name="subscribers_name" value="<?php echo $subscribers_name; ?>" />
				<input type="hidden" class="email" name="subscribers_email" value="<?php echo $subscribers_email; ?>" />
				<input type="hidden" class="token" name="subscribers_token" value="<?php echo $subscribers_token; ?>" />
				
				<div class="input-group-sm-append">
				<input type="hidden" name="category" value="category" />	<!-- to distinguish if delete or update button -->
					<button class="btn btn-sm btn-primary submit-single" type="submit" name="button_category">Update</button>
				</div>
			</div>											
		
	</td>
	<td>
		<!-- Delete button -->
			<input type="hidden" name="delete" value="delete" />	<!-- to distinguish if delete or update button -->													
			<button class="btn btn-sm btn-danger submit-single" type="submit" name="button_delete">Delete</button>				
	
	</td>
</tr>	