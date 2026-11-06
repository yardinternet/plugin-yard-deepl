<?php

use YDPL\Repositories\TranslationRepository;

beforeEach(
	function () {
		given_post( 1, '2026-10-01 10:00:00' );
		given_cached_translation( 1, 'EN-GB', array( pair( 'Menu', 'Menu EN' ) ), '2026-10-02 10:00:00' );
		update_post_meta( 1, '_ydpl_incomplete_request_count_EN-GB', 3 );
		$this->repository = new TranslationRepository();
	}
);

it(
	'reports incomplete request counts per language',
	function () {
		$column_data = $this->repository->get_column_data( 1, array( 'EN-GB', 'DE' ) );

		expect( $column_data['incomplete_request_counts'] )->toBe( array( 'EN-GB' => 3 ) );
	}
);

it(
	'resets the incomplete request count when a translation is stored',
	function () {
		$this->repository->store_translation( 1, 'EN-GB', array( pair( 'Menu', 'Menu EN' ) ) );

		expect( get_post_meta( 1, '_ydpl_incomplete_request_count_EN-GB', true ) )->toBe( '' );
	}
);

it(
	'deletes the request counts together with the cached translations',
	function () {
		update_post_meta( 1, '_ydpl_uncached_request_count_DE', 5 );

		$this->repository->delete_cached_translations( 1 );

		expect( get_post_meta( 1 ) )->toBe( array() );
	}
);
