<?php
	foreach($post_types as $post_type_key => $post_type_val):
		if($post_type_key == 'attachment') continue; ?>

			<label for="post_type_<?php echo $post_type_key;?>">
				<input name="apx-link-status-setting[post_type][<?php echo $post_type_key;?>]" type="checkbox" id="post_type_<?php echo $post_type_key;?>" value="<?php echo $post_type_val;?>" <?php checked($post_type_val, @$setting['post_type'][$post_type_key]); ?> />
				<?php echo esc_html($post_type_val);?>
			</label><br>
		<?php
	endforeach;?>