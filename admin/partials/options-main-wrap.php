<div class="wrap">
	<h2><?php esc_html_e('APX Link Status Options','apx-link-status');?></h2>
	<form action="options.php" method="POST">
		<?php settings_fields( 'apx-link-status-setting-group' ); ?>
		<?php do_settings_sections( 'apx-link-status-section' ); ?>
		<?php submit_button(); ?>
	</form>
	<hr/>
</div>