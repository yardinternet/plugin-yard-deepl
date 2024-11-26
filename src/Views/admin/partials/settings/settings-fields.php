<?php
/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$api_key                               = $api_key ?? '';
$settings_field_id                     = $settings_field_id ?? '';
$supported_languages                   = is_array( $supported_languages ?? null ) ? $supported_languages : array();
$configured_supported_languages        = is_array( $configured_supported_languages ?? null ) ? $configured_supported_languages : array();
$rest_api_param_object_id_is_mandatory = $rest_api_param_object_id_is_mandatory ?? true;
?>

<?php if ( $settings_field_id === 'ydpl_api_key' ) : ?>
<input type="password" name="ydpl_options[ydpl_api_key]" value="<?php echo esc_attr( $api_key ); ?>">
<?php endif; ?>

<?php if ( $settings_field_id === 'ydpl_supported_target_languages' ) : ?>
<select name="ydpl_options[ydpl_supported_target_languages][]" multiple>
	<?php foreach ( $supported_languages as $value ) : ?>
	<option value="<?php echo esc_attr( $value['iso_alpha2'] ?? '' ); ?>" <?php echo in_array( $value['iso_alpha2'] ?? '', $configured_supported_languages, true ) ? 'selected' : ''; ?>>
		<?php echo esc_html( $value['name'] ?? '' ); ?>
	</option>
	<?php endforeach; ?>
</select>
<?php endif; ?>

<?php if ( $settings_field_id === 'ydpl_rest_api_param_object_id_is_mandatory' ) : ?>
<input type="checkbox" name="ydpl_options[ydpl_rest_api_param_object_id_is_mandatory]" <?php echo $rest_api_param_object_id_is_mandatory ? 'checked' : ''; ?>>
<?php endif; ?>
