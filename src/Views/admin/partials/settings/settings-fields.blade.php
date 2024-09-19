@php
	$api_key = $api_key ?? '';
	$settings_field_id = $settings_field_id ?? '';
	$supported_languages = $supported_languages ?? [];
	$configured_supported_languages = $configured_supported_languages ?? [];
	$rest_api_param_object_id_is_mandatory = $rest_api_param_object_id_is_mandatory ?? true;
@endphp

@if ($settings_field_id === 'ydpl_api_key')
	<input type="password" name="ydpl_options[ydpl_api_key]" value="{{ !empty($api_key) ? esc_attr($api_key) : '' }}">
@endif

@if ($settings_field_id === 'ydpl_supported_target_languages' && is_array($supported_languages))
	<select name="ydpl_options[ydpl_supported_target_languages][]" multiple>
		@foreach ($supported_languages as $value)
			<option value="{{ esc_attr($value['iso_alpha2']) }}"
				{{ in_array($value['iso_alpha2'], $configured_supported_languages) ? 'selected' : '' }}>
				{{ esc_html($value['name']) }}
			</option>
		@endforeach
	</select>
@endif

@if ($settings_field_id === 'ydpl_rest_api_param_object_id_is_mandatory')
	<input type="checkbox" name="ydpl_options[ydpl_rest_api_param_object_id_is_mandatory]"
		{{ !empty($rest_api_param_object_id_is_mandatory) ? 'checked' : '' }}>
@endif
