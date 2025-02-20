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
if (!defined('ABSPATH')) {
	exit;
}

use YDPL\Contracts\ServiceProviderInterface;
use YDPL\Singletons\SiteOptionsSingleton;

class TextExtractionServiceProvider implements ServiceProviderInterface
{
	protected SiteOptionsSingleton $options;

	public function __construct()
	{
		$this->options = ydpl_resolve_from_container('ydpl.site_options');
	}

	/**
	 * Register the service provider.
	 *
	 * @since 0.0.1
	 */
	public function register(): void
	{
		add_action('save_post', [$this, 'action_save_post']);
	}

	/**
	 * Save post action.
	 *
	 * @param int $post_id The ID of the post being saved.
	 *
	 *
	 * @return void
	 */
	public function action_save_post(int $object_id): void
	{
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		if (defined('DOING_AJAX') && DOING_AJAX) {
			return;
		}

		if (defined('REST_REQUEST') && REST_REQUEST) {
			return;
		}

		add_action('shutdown', function () use ($object_id) {
			$this->get_allowed_text($object_id, true);

			// Unfortunately, we have no way of knowing which URL was updated, so we have to refresh all of them.
			global $wpdb;
			$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'ydpl-cache-%'");
		});
	}

	/**
	 * Get allowed text array for a post, or a URL.
	 *
	 * @param string $object The object to get the allowed text for.
	 * @param bool   $refresh
	 * @return array
	 */
	public function get_allowed_text(string $object, bool $refresh = false): array
	{
		list($type, $resource_identifier) = explode('-', $object, 2);
		switch ($type) {
			case 'post':
				// Get the stored list of allowed texts from post-meta.
				$allowed_text = get_post_meta($resource_identifier, 'ydpl_allowed_text', true);
				if (!empty($allowed_text) && !$refresh) {
					return $allowed_text['text'] ?? [];
				}
				// If we have no cache at all, we build it, cache it and return it.
				$url = get_permalink($resource_identifier);
				if (!$url) {
					return [];
				}
				$allowed_text = $this->extract_text($url);
				update_post_meta($resource_identifier, 'ydpl_allowed_text', ['text' => $allowed_text, 'timestamp' => microtime(true)]);
				break;
			case 'url':
				// Get the stored list of allowed texts from post-meta.
				$url = $resource_identifier;
				$resource_identifier = 'ydpl-cache-' . md5($url);
				$allowed_text = get_option($resource_identifier);
				if (!empty($allowed_text) && !$refresh) {
					return $allowed_text['text'] ?? [];
				}
				// If we have no cache at all, we build it, cache it and return it.
				$allowed_text = $this->extract_text($url);
				// check if the option exists, if not, add it with autoload set to 'no'.
				$data = ['text' => $allowed_text, 'timestamp' => microtime(true), 'url' => $url];
				if (false === get_option($resource_identifier)) {
					add_option($resource_identifier, $data, '', 'no');
				} else {
					update_option($resource_identifier, $data);
				}
				break;
		}

		return $allowed_text;
	}

	/**
	 * Extract text array from a URL.
	 *
	 * @param string $url The URL to extract text from.
	 *
	 * @return array
	 */
	public function extract_text(string $url)
	{
		// Preserve user state.
		$cookies = $_COOKIE;
		$content = wp_remote_get($url, [
			'cookies' => $cookies,
			'referer' => $url,
		]);

		if (is_wp_error($content)) {
			return [];
		}

		$content = wp_remote_retrieve_body($content);

		if (!$content) {
			return [];
		}

		// Use DOM and xpath to extract the content.
		$dom = new \DOMDocument();
		@$dom->loadHTML($content);
		$xpath = new \DOMXPath($dom);
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
		foreach ($content_selector as $selector) {
			$where = 'text';
			if (str_contains($selector, '[')) {
				$selector = str_replace('[', '[@', $selector);
				// For now, this fits the bill. We can expand this later.
				$where = 'attr-value';
			}
			$nodes = $xpath->query("*/{$selector}");

			$allowed_text = array_merge($allowed_text, self::extract_from_dom_nodes($nodes, $where));
		}

		foreach ($nodes as $node) {
			$allowed_text[] = trim($node->textContent);
		}
		if ($allowed_text) {
			$allowed_text = array_unique(array_filter(array_map('trim', $allowed_text)), SORT_STRING);
			usort($allowed_text, 'strcasecmp');
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
	public function array_intersect_loose(mixed $text, array $intersect): array
	{
		return array_uintersect($text, $intersect, [$this, 'compare_function']);
	}

	/**
	 * Normalize a string for comparison.
	 *
	 * @param string $string_to_normalize The string to normalize.
	 * @param string $encoding            The encoding of the string.
	 *
	 * @return string
	 */
	private static function normalize_string($string_to_normalize, $encoding = "UTF-8")
	{
		$string_to_normalize = trim($string_to_normalize);
		$string_to_normalize = preg_replace('/\s+/', ' ', $string_to_normalize);
		$string_to_normalize = preg_replace('/&([^;])[^;]*;/', "$1", htmlentities(mb_strtolower($string_to_normalize, $encoding), null, $encoding));

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
	private static function compare_function($a, $b)
	{
		return strcmp(self::normalize_string($a), self::normalize_string($b));
	}

	/**
	 * Extract text from DOM nodes, recursively.
	 *
	 * @param \DOMNodeList $nodes List of DOM nodes.
	 *                            Not strong typed in the signature to prevent errors in case a different library version gives a slightly different object.
	 *
	 * @return array
	 */
	private static function extract_from_dom_nodes($nodes, $where = 'text'): array
	{
		$allowed_text = [];
		foreach ($nodes as $node) {
			$cnodes = $node->childNodes;
			foreach ($cnodes as $cnode) { // we want 'if $cnodes', but that doesn't seem to work. Revisit.
				$allowed_text = array_merge($allowed_text, self::extract_from_dom_nodes($cnodes, $where));
				continue 2;
			}
			list($where, $what) = explode('-', $where . '-unknown');
			switch ($where) {
				case 'text':
				default:
					$allowed_text[] = trim($node->nodeValue);
					break;
				case 'attr':
					$allowed_text[] = trim($node->getAttribute($what));
					break;
			}

		}

		return $allowed_text;
	}
}
