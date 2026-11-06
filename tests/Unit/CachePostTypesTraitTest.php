<?php

use YDPL\Traits\CachePostTypesTrait;

it(
	'supports every public post type except attachments by default',
	function () {
		$subject = new class() {
			use CachePostTypesTrait;

			public function post_types(): array
			{
				return $this->get_cache_metabox_post_types();
			}
		};

		expect( $subject->post_types() )->toBe( array( 'post', 'page', 'pdc-item' ) );
	}
);
