<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://alignpixel.com
 * @since      1.0.0
 *
 * @package    APx_Link_Status
 * @subpackage APx_Link_Status/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="apx-link-wrap">
	<div class="apx-link-count"><?php esc_html_e('Total Links:','apx-link-status');?> <?php echo esc_html($link_details['info']['total']);?></div>
	<div class="apx-link-count"><?php esc_html_e('Internal Links:','apx-link-status');?> <?php echo esc_html($link_details['info']['internal']);?></div>
	<div class="apx-link-count"><?php esc_html_e('External Links:','apx-link-status');?> <?php echo esc_html($link_details['info']['external']);?></div>
</div>

<?php if($link_details['info']['total'] != 0): ?>

	<table class="apx-table">
		<tr>
			<th><?php esc_html_e('Link Type','apx-link-status');?></th>
			<th><?php esc_html_e('Anchor Text','apx-link-status');?></th>
			<th><?php esc_html_e('Link','apx-link-status');?></th>
		</tr>
				
		<?php foreach($link_details['details'] as $val):?>
			<tr>
				<td>
					<?php 
					if($val['link_type'] == 'External'):
						echo '<span class="red-text">'.esc_html($val['link_type']).'</span>';
					else:
						echo esc_html($val['link_type']);
					endif;
					?>
				</td>
				<td><?php echo esc_html($val['anchor']);?></td>
				<td><?php echo esc_html($val['link']);?></td>
			</tr>
		<?php endforeach;?>
	</table>

	<?php
endif;?>