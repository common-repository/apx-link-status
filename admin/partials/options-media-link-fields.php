<?php
	foreach($media_arr as $media_arr_key => $media_arr_val):?>
		<label for="post_type_<?php echo $media_arr_key;?>">
			<input name="apx-link-status-setting[media][<?php echo $media_arr_key;?>]" type="checkbox" id="post_type_<?php echo $media_arr_key;?>" value="<?php echo $media_arr_val;?>" <?php checked($media_arr_val, @$setting['media'][$media_arr_key]); ?> />
			<?php echo esc_html($media_arr_val);?>
		</label><br>
		<?php
	endforeach;
?>