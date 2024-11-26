<?php
/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
?>
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<form action="<?php echo esc_url( 'options.php' ); ?>" method="post">
		<?php
		settings_fields( 'ydpl_options_group' );
		do_settings_sections( 'yard-deepl' );
		submit_button( __( 'Save', 'yard-deepl' ) );
		?>
	</form>
</div>
