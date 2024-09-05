@php
	$api_key = $api_key ?? '';
	$settings_field_id = $settings_field_id ?? '';
	$supported_languages = $supported_languages ?? [];
	$configured_supported_languages = $configured_supported_languages ?? [];
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
