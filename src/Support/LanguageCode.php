<?php

namespace YDPL\Support;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since 2.2.0
 */
class LanguageCode
{
	/**
	 * Reduces a BCP-47 tag or WordPress locale to the bare ISO-639-1 code
	 * DeepL accepts as `source_lang`.
	 *
	 * DeepL rejects regional tags on `source_lang` with HTTP 400, so
	 * 'nl-NL' and 'nl_NL' both have to become 'NL'. Returns an empty string
	 * when the value cannot be used, so callers can fall through.
	 *
	 * @since 2.2.0
	 */
	public static function normalize( string $value ): string
	{
		$parts          = preg_split( '/[-_]/', trim( $value ) );
		$primary_subtag = $parts[0] ?? '';

		if ( ! preg_match( '/^[A-Za-z]{2}$/', $primary_subtag ) ) {
			return '';
		}

		return strtoupper( $primary_subtag );
	}
}
