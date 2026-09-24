<?php

namespace YDPL\Support;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @since NEXT
 */
class LanguageCode
{
	/**
	 * Language codes DeepL accepts as `source_lang`.
	 * Source: https://developers.deepl.com/docs/getting-started/supported-languages
	 *
	 * @since NEXT
	 *
	 * @var string[]
	 */
	public const SUPPORTED_SOURCE_LANGUAGES = array(
		'AR',
		'BG',
		'CS',
		'DA',
		'DE',
		'EL',
		'EN',
		'ES',
		'ET',
		'FI',
		'FR',
		'HU',
		'ID',
		'IT',
		'JA',
		'KO',
		'LT',
		'LV',
		'NB',
		'NL',
		'PL',
		'PT',
		'RO',
		'RU',
		'SK',
		'SL',
		'SV',
		'TR',
		'UK',
		'ZH',
	);

	/**
	 * Reduces a BCP-47 tag or WordPress locale to the bare ISO-639-1 code
	 * DeepL accepts as `source_lang`.
	 *
	 * @since NEXT
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

	/**
	 * Tells whether DeepL accepts this exact code as `source_lang`.
	 *
	 * @since NEXT
	 */
	public static function is_supported_source( string $code ): bool
	{
		return in_array( strtoupper( trim( $code ) ), self::SUPPORTED_SOURCE_LANGUAGES, true );
	}

	/**
	 * Normalizes a tag and keeps it only when DeepL supports it as a source
	 * language, so an empty return means "let DeepL auto-detect".
	 *
	 * @since NEXT
	 */
	public static function to_source_language( string $value ): string
	{
		$code = self::normalize( $value );

		return self::is_supported_source( $code ) ? $code : '';
	}
}
