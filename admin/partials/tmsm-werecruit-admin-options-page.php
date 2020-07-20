<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Tmsm_Werecruit
 * @subpackage Tmsm_Werecruit/admin/partials
 */
?>
<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

<p><?php echo '<a href="' . admin_url( 'customize.php') . '">'.__('Customize colors', 'tmsm-werecruit').'</a>' ?></p>

<form method="post" action="options.php"><?php
	settings_fields( $this->plugin_name . '-options' );
	do_settings_sections( $this->plugin_name );
	submit_button( __( 'Save options', 'tmsm-werecruit' ));

	do_action( 'tmsmwerecruit_cronaction' );
	?></form>