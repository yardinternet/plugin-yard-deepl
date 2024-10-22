@php
	// Translators: %s is the URL to the Deepl API documentation page.
	$website_url =
	    '<a href="https://developers.deepl.com/docs/getting-started/auth">' . esc_html__('website', 'yard-deepl') . '</a>';
@endphp
<p>
	@php
		printf(
		    // Translators: %s is the link to the Deepl API documentation website.
		    esc_html__(
		        'To query the Deepl API, an API key is required. You can find more information on their %s.',
		        'yard-deepl',
		    ),
		    $website_url,
		);
	@endphp
</p>
