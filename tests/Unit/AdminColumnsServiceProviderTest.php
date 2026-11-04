<?php

use YDPL\Providers\AdminColumnsServiceProvider;

beforeEach( function () {
	$GLOBALS['ydpl_test']['languages'] = array( 'EN-GB' );
	given_post( 1, '2026-10-01 10:00:00' );
	given_cached_translation( 1, 'EN-GB', array( pair( 'Menu', 'Menu EN' ) ), '2026-10-02 10:00:00' );
} );

function rendered_column(): string
{
	ob_start();
	( new AdminColumnsServiceProvider() )->render_column( 'ydpl_translation_cache', 1 );

	return ob_get_clean();
}

it( 'shows a cached language without incomplete responses as cached', function () {
	expect( rendered_column() )->toContain( 'ydpl-cache-badge--cached' );
} );

it( 'shows a cached language with incomplete responses as incomplete', function () {
	update_post_meta( 1, '_ydpl_incomplete_request_count_EN-GB', 2 );

	expect( rendered_column() )
		->toContain( 'ydpl-cache-badge--incomplete" title="2 incomplete responses">EN-GB' )
		->not->toContain( 'ydpl-cache-badge--cached' );
} );
