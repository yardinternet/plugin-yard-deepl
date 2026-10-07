<?php

use YDPL\Controllers\RestAPIController;
use YDPL\Services\TranslationService;

function controller( TranslationService $service ): RestAPIController
{
	return new class( $service ) extends RestAPIController {
		public function __construct( TranslationService $service )
		{
			parent::__construct();
			$this->service = $service;
		}
	};
}

function translate_request( array $text ): WP_REST_Request
{
	return new WP_REST_Request(
		array(
			'text'        => $text,
			'target_lang' => 'EN-GB',
			'object_id'   => 1,
		)
	);
}

function exhaust_rate_limit( RestAPIController $controller ): void
{
	foreach ( range( 1, 3 ) as $i ) {
		$controller->handle_translate_request( translate_request( array( "Tekst $i" ) ) );
	}
}

beforeEach( function () {
	given_post( 1, '2026-10-01 10:00:00' );
	given_cached_translation( 1, 'EN-GB', array( pair( 'Menu', 'Menu EN' ), pair( 'Admin bar', 'Admin bar EN' ) ), '2026-10-02 10:00:00' );
	$this->service    = fake_deepl_service();
	$this->controller = controller( $this->service );
} );

it( 'does not count fully cached requests towards the rate limit', function () {
	foreach ( range( 1, 5 ) as $i ) {
		$response = $this->controller->handle_translate_request( translate_request( array( 'Menu' ) ) );
	}

	expect( $response->status )->toBe( 200 )
		->and( $response->data )->toBe( array( pair( 'Menu', 'Menu EN' ) ) );
} );

it( 'serves visitors only the cached texts without calling DeepL or counting towards the rate limit', function () {
	foreach ( range( 1, 5 ) as $i ) {
		$response = $this->controller->handle_translate_request( translate_request( array( 'Menu', 'Goedemiddag' ) ) );
	}

	expect( $response->status )->toBe( 200 )
		->and( $response->data )->toBe( array( pair( 'Menu', 'Menu EN' ) ) )
		->and( $this->service->deepl_requests )->toBe( array() );
} );

it( 'translates texts missing from the cache for users with the cache capability', function () {
	$GLOBALS['ydpl_test']['can_cache'] = true;

	$response = $this->controller->handle_translate_request( translate_request( array( 'Menu', 'Goedemiddag' ) ) );

	expect( $response->data )->toBe( array( pair( 'Menu', 'Menu EN' ), pair( 'Goedemiddag', 'EN-GB:Goedemiddag' ) ) )
		->and( $this->service->deepl_requests )->toBe( array( array( 'Goedemiddag' ) ) );
} );

it( 'rejects a rate limited request when nothing is cached', function () {
	exhaust_rate_limit( $this->controller );

	$response = $this->controller->handle_translate_request( translate_request( array( 'Goedemiddag' ) ) );

	expect( $response->status )->toBe( 429 );
} );
