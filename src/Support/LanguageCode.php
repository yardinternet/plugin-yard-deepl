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
	 * Language codes DeepL accepts as `source_lang`.
	 *
	 * Source: https://developers.deepl.com/docs/getting-started/supported-languages,
	 * checked 2026-09-23. This is the conservative core set that DeepL's classic
	 * translation model supports, which is the model this plugin gets: DeeplClient
	 * sends no `model_type`, so DeepL uses its default. The much wider set that
	 * arrived with the next-generation model is deliberately left out, and so are
	 * regional variants such as 'EN-GB', which DeepL takes only as a target.
	 *
	 * Leaving a code out is safe: the caller then omits `source_lang` and DeepL
	 * auto-detects. Listing a code DeepL does not accept is not safe, because
	 * DeepL answers a request carrying it with HTTP 400.
	 *
	 * @since 2.2.0
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

	/**
	 * Tells whether DeepL accepts this exact code as `source_lang`.
	 *
	 * The two-letter shape check in normalize() is not enough on its own:
	 * 'FY', 'LB' and 'IS' are all well formed yet unsupported, and DeepL
	 * answers them with HTTP 400 instead of falling back to auto-detect.
	 *
	 * @since 2.2.0
	 */
	public static function is_supported_source( string $code ): bool
	{
		return in_array( strtoupper( trim( $code ) ), self::SUPPORTED_SOURCE_LANGUAGES, true );
	}

	/**
	 * Normalizes a tag and keeps it only when DeepL supports it as a source
	 * language, so an empty return means "let DeepL auto-detect".
	 *
	 * Auto-detect is imperfect on short strings, but it is never worse than the
	 * HTTP 400 an unsupported code earns, and it beats guessing Dutch for a
	 * Frisian or Low-Saxon site.
	 *
	 * @since 2.2.0
	 */
	public static function to_source_language( string $value ): string
	{
		$code = self::normalize( $value );

		return self::is_supported_source( $code ) ? $code : '';
	}
}
