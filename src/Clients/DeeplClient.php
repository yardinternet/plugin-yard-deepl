<?php

namespace YDPL\Clients;

use Exception;
use YDPL\Support\LanguageCode;

class DeeplClient
{
	public const DEFAULT_BASE_URL = 'https://api.deepl.com/v2/translate';

	private string $apiKey;
	private string $baseUrl;

	public function __construct( string $apiKey, string $baseUrl = '' )
	{
		$this->apiKey  = $apiKey;
		$this->baseUrl = '' !== $baseUrl ? $baseUrl : self::DEFAULT_BASE_URL;
	}

	/**
	 * @since 1.0.0
	 *
	 * Translate text using the DeepL API.
	 *
	 * @throws Exception If the API call fails.
	 */
	public function translateText( array $text, string $targetLang, string $sourceLang = '' ): array
	{
		$response = $this->makeRequest( self::buildPayload( $text, $targetLang, $sourceLang ) );

		if ( isset( $response['translations'] ) && is_array( $response['translations'] ) ) {
			return $response['translations'];
		}

		throw new Exception( sprintf( 'DeepL API: Unexpected response: %s', json_encode( $response ) ) );
	}

	/**
	 * Builds the /v2/translate request body.
	 *
	 * `source_lang` is left out of the body altogether unless DeepL supports the
	 * code, because DeepL answers an unsupported one with HTTP 400 and this
	 * client turns that into a 500 for the whole request. An absent key makes
	 * DeepL auto-detect, which is the last fallback the resolution chain wants.
	 * The key has to be genuinely absent: an empty string is a 400 as well.
	 *
	 * This is the single choke point for the payload, so no caller can reach
	 * DeepL with a code it does not accept.
	 *
	 * @since 2.2.0
	 */
	public static function buildPayload( array $text, string $targetLang, string $sourceLang ): array
	{
		$payload = array(
			'text'        => $text,
			'target_lang' => $targetLang,
		);

		if ( LanguageCode::is_supported_source( $sourceLang ) ) {
			$payload['source_lang'] = strtoupper( trim( $sourceLang ) );
		}

		return $payload;
	}

	/**
	 * @since 1.0.0
	 *
	 * Make a POST request to the DeepL API using wp_remote_post.
	 *
	 * @throws Exception If the API call fails.
	 */
	private function makeRequest( array $payload ): array
	{
		$response = wp_remote_post(
			$this->baseUrl,
			array(
				'headers' => array(
					'Authorization' => sprintf( 'DeepL-Auth-Key %s', $this->apiKey ),
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode( $payload ),
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			throw new Exception( sprintf( 'DeepL API: WP_Error: %s', $response->get_error_message() ) );
		}

		$httpCode = wp_remote_retrieve_response_code( $response );
		$body     = wp_remote_retrieve_body( $response );

		if ( 200 !== $httpCode ) {
			throw new Exception( sprintf( 'DeepL API: HTTP error %d: %s', $httpCode, $body ) );
		}

		return json_decode( $body, true );
	}
}
