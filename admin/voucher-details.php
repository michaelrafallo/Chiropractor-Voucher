<style type="text/css">
.image-preview img {
	max-width: 350px;
	width: 100%;
}
#voucher_details label {
	margin: 5px 0;
	font-weight: bold;
    display: inline-block;
}
#titlediv .inside {
	display: none;
}
</style>

<input type="hidden" name="meta_box_nonce" value="<?php echo wp_create_nonce( basename(__FILE__) ); ?>">

<?php $gforms = GFAPI::get_forms(); ?>

<p>
	<label for="fields[form]">Form</label>
	<br>
	<select name="fields[form]" id="fields[form]" class="regular-text">
		<option value="">Select Form</option>
			<?php foreach($gforms as $gform): 
				$form_title = $gform['title'];
				$form_id = $gform['fields'][0]['formId'];
			?>
			<option value="<?php echo $form_id; ?>" <?php selected( $form, $form_id ); ?>>(ID : <?php echo $form_id; ?>) <?php echo $form_title; ?></option>
		<?php endforeach; ?>
	</select>
</p>

<p>
	<label for="fields[expiration_days]">Expiration Days</label>
	<br>
	<input type="number" name="fields[expiration_days]" id="fields[expiration_days]" class="regular-text" value="<?php echo $expiration_days; ?>" required>
</p>

<div class="browse-image">
	<label for="fields[image]">Image Template</label><br>
	<input type="text" name="fields[image]" id="fields[image]" class="meta-image regular-text" value="<?php echo $image; ?>">
	<input type="button" class="button image-upload" value="Browse">
	<p class="image-preview">
		<?php if( @$image ): ?>
	    <img src="<?php echo $image; ?>">		
	<?php endif; ?>
	</p>
</div>

<?php $pages = get_pages(); ?>

<p>
	<label for="fields[completion_page]">Completion Page</label>
	<br>
	<select name="fields[completion_page]" id="fields[completion_page]" class="regular-text">
		<option value="">Select Completion Page</option>
		<?php foreach($pages as $page): ?>
			<option value="<?php echo $page->ID; ?>" <?php selected( $completion_page, $page->ID ); ?>>(ID : <?php echo $page->ID; ?>) <?php echo $page->post_title; ?></option>
		<?php endforeach; ?>
	</select>
</p>

<p>
	Use the shortcode in the selected completion page anywhere in the content.
	<input type="text" value="[voucher]" style="text-align: center;" readonly>
</p>

<?php $tp = get_post_meta( $post->ID, 'text_position', true ); ?>

<div style="margin-bottom:10px;">
	<table>
		<tr>
			<th></th>
			<th>( X )</th>
			<th>( Y )</th>
			<th>Font Size</th>
			<th>Text Limit</th>
		</tr>
		<tr>
			<td>Name</td>
			<td><input type="number" name="text_position[name][x]" value="<?php echo @$tp['name']['x']; ?>" placeholder="1875"></td>
			<td><input type="number" name="text_position[name][y]" value="<?php echo @$tp['name']['y']; ?>"  placeholder="735"></td>
			<td><input type="number" name="text_position[name][font_size]" value="<?php echo @$tp['name']['font_size']; ?>"  placeholder="40"></td>
			<td><input type="number" name="text_position[name][limit]" value="<?php echo @$tp['name']['limit']; ?>"  placeholder="18"></td>
		</tr>
		<tr>
			<td>Expiration</td>
			<td><input type="number" name="text_position[exp][x]" value="<?php echo @$tp['exp']['x']; ?>"  placeholder="2070"></td>
			<td><input type="number" name="text_position[exp][y]" value="<?php echo @$tp['exp']['y']; ?>"  placeholder="1052"></td>
			<td><input type="number" name="text_position[exp][font_size]" value="<?php echo @$tp['exp']['font_size']; ?>"  placeholder="38"></td>
			<td align="center">--</td>
		</tr>
	</table>

	<a href="#" class="generate-output">Generate Output</a>	

</div>

<div class="display-output"></div>

<input type="hidden" name="" value="generate_output" class="h-action">

<script type="text/javascript">
js = jQuery;	
jQuery(document).on('click', '.generate-output', function(e) {
e.preventDefault();
 	formData = js('#post');
    js('.display-output').html('<b>Generating sample output ...</b>');
    js('.h-action').attr('name', 'action');
    js.ajax({
        url: "<?php echo site_url( 'wp-admin/admin-ajax.php' ); ?>", 
        type: "POST",  
        data: new FormData(formData[0]),
        contentType: false,  
        cache: false,         
        processData:false,    
        success: function(response) {
           js('.display-output').html(response);
		   js('.h-action').attr('name', '');
        }
    });
});
</script>	

