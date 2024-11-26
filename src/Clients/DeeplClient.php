<?php

namespace YardDeepl\Clients;

use Exception;

class DeeplClient
{
	private string $apiKey;
	private string $baseUrl = 'https://api.deepl.com/v2/translate';

	public function __construct( string $apiKey )
	{
		$this->apiKey = $apiKey;
	}

	/**
	 * @since 1.0.0
	 *
	 * Translate text using the DeepL API.
	 *
	 * @throws Exception If the API call fails.
	 */
	public function translateText( array $text, string $targetLang ): array
	{
		$payload = array(
			'text'        => $text,
			'target_lang' => $targetLang,
		);

		$response = $this->makeRequest( $payload );

		if ( isset( $response['translations'] ) && is_array( $response['translations'] ) ) {
			return $response['translations'];
		}

		throw new Exception( sprintf( 'DeepL API: Unexpected response: %s', json_encode( $response ) ) );
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
