<?php

define( 'ABSPATH', __DIR__ );
define( 'SECURE_AUTH_KEY', 'test-key' );

class WP_Post
{
	public string $post_modified = '';
}

class WP_REST_Request
{
	public function __construct( private array $params )
	{
	}

	public function get_param( string $key )
	{
		return $this->params[ $key ] ?? null;
	}

	public function get_header( string $key )
	{
		return null;
	}
}

class WP_REST_Response
{
	public function __construct( public $data = null, public int $status = 200 )
	{
	}
}

function get_post( int $id ): ?WP_Post
{
	return $GLOBALS['ydpl_test']['posts'][ $id ] ?? null;
}

function get_post_field( string $field, int $id ): string
{
	return get_post( $id )->$field;
}

function get_post_meta( int $id, string $key = '', bool $single = false )
{
	return $GLOBALS['ydpl_test']['meta'][ $id ][ $key ] ?? '';
}

function update_post_meta( int $id, string $key, $value ): void
{
	$GLOBALS['ydpl_test']['meta'][ $id ][ $key ] = $value;
}

function delete_post_meta( int $id, string $key ): void
{
	unset( $GLOBALS['ydpl_test']['meta'][ $id ][ $key ] );
}

function current_time( string $type ): string
{
	return '2026-10-06 12:00:00';
}

function current_user_can( string $capability ): bool
{
	return $GLOBALS['ydpl_test']['can_cache'];
}

function apply_filters( string $hook, $value )
{
	return $value;
}

function get_transient( string $key )
{
	return $GLOBALS['ydpl_test']['transients'][ $key ] ?? false;
}

function set_transient( string $key, $value, int $expiration ): void
{
	$GLOBALS['ydpl_test']['transients'][ $key ] = $value;
}

function ydpl_resolve_from_container( string $key )
{
	return YDPL\Singletons\SiteOptionsSingleton::get_instance( array() );
}

function fake_deepl_service(): YDPL\Services\TranslationService
{
	return new class() extends YDPL\Services\TranslationService {
		public array $deepl_requests = array();

		public function handle_translation_without_object_id( array $text, string $target_lang ): array
		{
			$this->deepl_requests[] = $text;

			return array_map( fn ( $source ) => pair( $source, "$target_lang:$source" ), $text );
		}
	};
}

function given_post( int $id, string $post_modified ): void
{
	$post                = new WP_Post();
	$post->post_modified = $post_modified;

	$GLOBALS['ydpl_test']['posts'][ $id ] = $post;
}

function given_cached_translation( int $id, string $lang, array $translation, string $modified ): void
{
	update_post_meta( $id, "_translation_$lang", $translation );
	update_post_meta( $id, "_translation_modified_$lang", $modified );
}

function pair( string $text, string $translation ): array
{
	return array(
		'text'        => $text,
		'translation' => $translation,
	);
}

uses()->beforeEach( function () {
	$GLOBALS['ydpl_test'] = array(
		'posts'      => array(),
		'meta'       => array(),
		'transients' => array(),
		'can_cache'  => false,
	);
	$_SERVER['REMOTE_ADDR'] = '10.0.0.1';
} )->in( 'Unit' );
