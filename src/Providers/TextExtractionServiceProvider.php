<?php
/**
 * A Text-extraction service provider.
 *
 * @package    YDPL\Providers
 * @author     Remon Pel <remonpel@acato.nl>
 * @subpackage YDPL\Providers\TextExtractionServiceProvider
 */

namespace YDPL\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YDPL\Contracts\ServiceProviderInterface;
use YDPL\Singletons\SiteOptionsSingleton;

class TextExtractionServiceProvider implements ServiceProviderInterface {
	protected SiteOptionsSingleton $options;

	public function __construct() {
		$this->options = ydpl_resolve_from_container( 'ydpl.site_options' );
	}

	public function register(): void {
		add_action( 'save_post', array( $this, 'refresh_post_texts' ) );
	}

	public function refresh_post_texts( int $object_id ): void {
		add_action( 'shutdown', function () use ( $object_id ) {
			$this->get_allowed_text( $object_id, true );
		} );
	}

	public function get_allowed_text( int $object_id, bool $refresh = false ): array {
		// Get the stored list of allowed texts.
		$allowed_text = get_post_meta( $object_id, 'ydpl_allowed_text', true );
		if ( ! empty( $allowed_text ) && ! $refresh ) {
			return $allowed_text['text'] ?? [];
		}
		// If we have no cache at all, we build it, cache it and return it.
		$allowed_text = $this->extract_text( $object_id );
		update_post_meta( $object_id, 'ydpl_allowed_text', [ 'text' => $allowed_text, 'timestamp' => microtime( true ) ] );

		return $allowed_text;
	}

	public function extract_text( int $object_id ) {
		// Get the post content.
		$post = get_post( $object_id );
		if ( ! $post ) {
			return [];
		}
		$url = get_permalink( $object_id );

		// Preserve user state.
		$cookies = $_COOKIE;
		$content = wp_remote_get( $url, [
			'cookies' => $cookies,
			'referer' => $url,
		] );

		if ( is_wp_error( $content ) ) {
			return [];
		}

		$content = wp_remote_retrieve_body( $content );

		if ( ! $content ) {
			return [];
		}

		// Use DOM and xpath to extract the content.
		$dom = new \DOMDocument();
		@$dom->loadHTML( $content );
		$xpath            = new \DOMXPath( $dom );
		$content_selector = [
			// A list of jQuery / CSS selectors to extract text from. We wil translate this list to xpath compatible selectors, this is this way for ease of maint.
			'div',
			'p',
			'span',
			'h1',
			'h2',
			'h3',
			'h4',
			'h5',
			'h6',
			'li',
			'button',
			'blockquote',
			'a',
			'label',
			'details',
			'summary',
			'figcaption',
			'code',
			'pre',
			'th',
			'td',
			'textarea',
			'time',
			'input[type="button"]',
			'input[type="submit"]',
			'input[type="reset"]',
		];

		$allowed_text = [];
		foreach ( $content_selector as $selector ) {
			$where = 'text';
			if ( str_contains( $selector, '[' ) ) {
				$selector = str_replace( '[', '[@', $selector );
				// For now, this fits the bill. We can expand this later.
				$where = 'attr-value';
			}
			$nodes          = $xpath->query( "*/{$selector}" );

			$allowed_text = array_merge( $allowed_text, self::extract_from_dom_nodes( $nodes, $where ) );
		}

		foreach ( $nodes as $node ) {
			$allowed_text[] = trim( $node->textContent );
		}
		if ( $allowed_text ) {
			$allowed_text = array_unique( array_filter( array_map( 'trim', $allowed_text ) ), SORT_STRING );
			usort( $allowed_text, 'strcasecmp' );
		}

		return $allowed_text;
	}

	/**
	 * Array Intersect, but all strings are compared loosely to allow for accents, whitespace difference and case-insensitivity.
	 *
	 * @param string[] $text      List of texts to intersect. Items not in other lists will be removed.
	 * @param string[] $intersect List of texts to intersect with.
	 *
	 * @return array
	 */
	public function array_intersect_loose( mixed $text, array $intersect ): array {
		return array_uintersect( $text, $intersect, [ $this, 'compare_function' ] );
	}

	/**
	 * Normalize a string for comparison.
	 *
	 * @param string $string_to_normalize The string to normalize.
	 * @param string $encoding            The encoding of the string.
	 *
	 * @return string
	 */
	private static function normalize_string( $string_to_normalize, $encoding = "UTF-8" ) {
		$string_to_normalize = trim( $string_to_normalize );
		$string_to_normalize = preg_replace( '/\s+/', ' ', $string_to_normalize );
		$string_to_normalize = preg_replace( '/&([^;])[^;]*;/', "$1", htmlentities( mb_strtolower( $string_to_normalize, $encoding ), null, $encoding ) );

		return $string_to_normalize;
	}

	/**
	 * Internal compare function for array_uintersect, for loose comparison.
	 *
	 * @param string $a A string.
	 * @param string $b Another string.
	 *
	 * @return int
	 */
	private static function compare_function( $a, $b ) {
		return strcmp( self::normalize_string( $a ), self::normalize_string( $b ) );
	}

	/**
	 * Extract text from DOM nodes, recursively.
	 *
	 * @param \DOMNodeList $nodes List of DOM nodes.
	 *                            Not strong typed in the signature to prevent errors in case a different library version gives a slightly different object.
	 *
	 * @return array
	 */
	private static function extract_from_dom_nodes( $nodes, $where = 'text' ): array {
		$allowed_text = [];
		foreach ( $nodes as $node ) {
			$cnodes = $node->childNodes;
			foreach ( $cnodes as $cnode ) { // we want 'if $cnodes', but that doesn't seem to work. Revisit.
				$allowed_text = array_merge( $allowed_text, self::extract_from_dom_nodes( $cnodes, $where ) );
				continue 2;
			}
			list( $where, $what ) = explode('-', $where .'-unknown' );
			switch ( $where ) {
				case 'text':
				default:
					$allowed_text[] = trim( $node->nodeValue );
					break;
				case 'attr':
					$allowed_text[] = trim( $node->getAttribute($what) );
					break;
			}

		}

		return $allowed_text;
	}
}
