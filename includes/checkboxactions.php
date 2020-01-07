<table class="checkbox_actions d-none ">
	<tr>
		<th class="text-danger">For all selected:</th>		
	</tr>
	
	<tr>
		<th>Update Category</th>
		<th>Delete</th>
	</tr>
	<tr>		
		<td class="input-group input-group-sm">
			<select class="selectbox form-control" id="category" name="set_category">												
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
		
			<div class="input-group-sm-append">
				<input type="hidden" name="category" value="category" />	<!-- to distinguish if delete or update button -->
				<button class="btn btn-sm btn-primary submit-checkbox" type="submit" name="change_category">Update</button>
			</div>
		</td>
		<td>
			<input type="hidden" name="delete" value="delete" />	<!-- to distinguish if delete or update button -->
			<button class="btn btn-sm btn-danger submit-checkbox" type="submit">Delete</button>
		</td>
	</tr>
</table>
<br />