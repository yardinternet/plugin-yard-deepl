<?php

use YDPL\Repositories\TranslationRepository;

beforeEach(
	function () {
		given_post( 1, '2026-10-01 10:00:00' );
		given_cached_translation( 1, 'EN-GB', array( pair( 'Menu', 'Menu EN' ) ), '2026-10-02 10:00:00' );
		$this->repository = new TranslationRepository();
	}
);

it(
	'deletes the cached translations',
	function () {
		$this->repository->delete_cached_translations( 1 );

		expect( get_post_meta( 1 ) )->toBe( array() );
	}
);
