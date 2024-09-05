<div class="wrap">
	<h1>{{ esc_html(get_admin_page_title()) }}</h1>
	<form action="options.php" method="post">
		@php
			settings_fields('ydpl_options_group');
			do_settings_sections('yard-deepl');
			submit_button(__('Save', 'yard-deepl'));
		@endphp
	</form>
</div>
