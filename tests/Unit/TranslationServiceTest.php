<?php

beforeEach( function () {
	given_post( 1, '2026-10-01 10:00:00' );
	$this->service = fake_deepl_service();
} );

it( 'sends only texts missing from the cache to DeepL', function () {
	given_cached_translation( 1, 'EN-GB', array( pair( 'Menu', 'Menu EN' ) ), '2026-10-02 10:00:00' );

	$this->service->handle_translation_with_object_id( 1, array( 'Menu', 'Goedemiddag', 'Goedemiddag' ), 'EN-GB' );

	expect( $this->service->deepl_requests )->toBe( array( array( 'Goedemiddag' ) ) );
} );

it( 'does not call DeepL when every text is cached', function () {
	given_cached_translation( 1, 'EN-GB', array( pair( 'Menu', 'Menu EN' ) ), '2026-10-02 10:00:00' );

	$this->service->handle_translation_with_object_id( 1, array( 'Menu' ), 'EN-GB' );

	expect( $this->service->deepl_requests )->toBe( array() );
} );

it( 'returns exactly the requested texts in request order', function () {
	given_cached_translation( 1, 'EN-GB', array( pair( 'Menu', 'Menu EN' ), pair( 'Admin bar', 'Admin bar EN' ) ), '2026-10-02 10:00:00' );

	$translation = $this->service->handle_translation_with_object_id( 1, array( 'Nieuws', 'Menu', '2026' ), 'EN-GB' );

	expect( $translation )->toBe(
		array(
			pair( 'Nieuws', 'EN-GB:Nieuws' ),
			pair( 'Menu', 'Menu EN' ),
			pair( '2026', 'EN-GB:2026' ),
		)
	);
} );

it( 'merges new translations into the cache when the user may cache', function () {
	given_cached_translation( 1, 'EN-GB', array( pair( 'Menu', 'Menu EN' ) ), '2026-10-02 10:00:00' );
	update_post_meta( 1, '_ydpl_uncached_request_count_EN-GB', 4 );

	$this->service->handle_translation_with_object_id( 1, array( 'Nieuws' ), 'EN-GB', true );

	expect( get_post_meta( 1, '_translation_EN-GB', true ) )
		->toEqualCanonicalizing( array( pair( 'Nieuws', 'EN-GB:Nieuws' ), pair( 'Menu', 'Menu EN' ) ) )
		->and( get_post_meta( 1, '_ydpl_uncached_request_count_EN-GB', true ) )->toBe( '' );
} );

it( 'keeps the cache untouched and counts the request when the user may not cache', function () {
	given_cached_translation( 1, 'EN-GB', array( pair( 'Menu', 'Menu EN' ) ), '2026-10-02 10:00:00' );

	$this->service->handle_translation_with_object_id( 1, array( 'Nieuws' ), 'EN-GB' );

	expect( get_post_meta( 1, '_translation_EN-GB', true ) )->toBe( array( pair( 'Menu', 'Menu EN' ) ) )
		->and( get_post_meta( 1, '_ydpl_uncached_request_count_EN-GB', true ) )->toBe( 1 );
} );

it( 'ignores a cache older than the post and replaces it', function () {
	given_cached_translation( 1, 'EN-GB', array( pair( 'Menu', 'Old menu EN' ), pair( 'Oud', 'Old EN' ) ), '2026-09-30 10:00:00' );

	$translation = $this->service->handle_translation_with_object_id( 1, array( 'Menu' ), 'EN-GB', true );

	expect( $translation )->toBe( array( pair( 'Menu', 'EN-GB:Menu' ) ) )
		->and( get_post_meta( 1, '_translation_EN-GB', true ) )->toBe( array( pair( 'Menu', 'EN-GB:Menu' ) ) );
} );

it( 'does not cache when caching is disabled for the post', function () {
	update_post_meta( 1, 'ydpl_disable_deepl_translation_cache', '1' );

	$this->service->handle_translation_with_object_id( 1, array( 'Menu' ), 'EN-GB', true );

	expect( get_post_meta( 1, '_translation_EN-GB', true ) )->toBe( '' );
} );
