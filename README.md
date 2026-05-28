# Yard DeepL

Author: Yard Digital Agency
Author URI: <https://www.yard.nl>
Contributors: yarddigitalagency, mvdhoek1
License: EUPL v1.2
License URI: <https://www.gnu.org/licenses/gpl-2.0.html>
Requires at least: 6.0
Requires PHP: 8.0
Stable tag: 1.1.0
Tags: deepl, translating, secure
Tested up to: 6.8.5

This plugin registers secure API endpoints that allow you to request translations directly from DeepL without exposing your DeepL API-key.

## Description

This plugin registers secure API endpoints that allow you to request translations directly from DeepL without exposing your DeepL API-key. These endpoints are only accessible when a valid nonce is provided.
When providing translations to website visitors, you can configure which languages are supported for translation.

## Caching Mechanism

Each object that is translated will store its cached translation in the `wp_postmeta` table within the database. This caching mechanism ensures that translations are efficiently reused, reducing unnecessary API requests to DeepL and saving costs.

-   **Serving Cached Translations:** If a cached translation is newer than the `post_modified` date of the object, the cached version is served.
-   **Fetching New Translations:** When the `post_modified` date of the object is more recent than the cached translation, a new translation is fetched from DeepL. Once retrieved, this translation is immediately cached for future use.
-   **Cache Authorization:** Only logged-in users with the `edit_posts` capability (or a custom capability configured via the `yard::deepl/cache_capability` filter) are permitted to create new cache entries. Requests from users without this capability will still return a translation from DeepL, but the result will not be stored in the cache.

This approach minimizes the number of API calls to DeepL, ensuring translations are kept up to date only when necessary.

### Admin: Cache Metabox

A metabox labeled **Yard DeepL** is displayed on the edit screen of supported post types (default: `page`). It provides two options:

-   **Disable translation cache:** When checked, the cache is bypassed for this object. Useful for posts with dynamic content that should always be translated fresh.
-   **Clear translation cache:** When checked and the post is saved, all cached translations for this object are deleted.

### Admin: Translation Cache Column

A **Translation cache** column is added to the post list screen of all supported post types. Its purpose is to notify editors which posts should be cached as soon as possible to avoid unnecessary DeepL API costs.

-   **Green badge per language** (e.g. `NL`, `EN-US`): a valid, up-to-date cached translation exists for that language. Visitor requests are served from cache at no cost.
-   **Grey badge with a count per language** (e.g. `EN-US 42`): no cache exists for that language and visitors have triggered that many live DeepL API calls. Hovering the badge shows the full count as a tooltip. These posts are the most urgent to cache.
-   **—**: the post has never been requested for translation, or caching is disabled for this post.

The uncached call count is only incremented for visitor requests (users without cache-write capability) and is automatically reset per language once a cache entry is stored. Posts with the **Disable translation cache** option enabled are excluded from the column entirely.

## External Services

This plugin connects to the DeepL API to provide translations for content.

-   **Service:** DeepL API (<https://www.deepl.com>)
-   **Purpose:** To translate text from one language to another based on the provided target language.
-   **Data Sent:** Text content for translation, the target language code, and the DeepL API key (handled securely and never exposed to users).
-   **Conditions:** Data is sent when a request for translation is initiated.
-   **Privacy Policy:** [DeepL Privacy Policy](https://www.deepl.com/privacy)
-   **Terms of Service:** [DeepL Terms of Service](https://www.deepl.com/pro-license)

## Installation

1. Upload plugin directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## Development

The packages inside the `vendor` directory are namespace-prefixed. This is done by creating a new `vendor-prefixed` directory. After running `composer install`, this process happens automatically: the `vendor-prefixed` directory is generated on the fly, and the original `vendor` directory is deleted after installation.

If you need to work with development dependencies, follow these steps:

1. Run `composer install`. This will remove the `vendor` directory and create the `vendor-prefixed` directory.
2. Run `composer install --no-scripts`. The `--no-scripts` flag prevents the automatic deletion of the `vendor` directory, allowing you to work with the development dependencies. Ensure that the `vendor-prefixed` directory is still present, as the plug-in relies on it.

### Ready for releasing a new version?

1. Run `composer install --optimize-autoloader`. This will optimize the autoloading rules by converting them into a class map, which improves performance and speeds up autoloading. It is particularly useful for production environments.

## Usage

### Security

The API endpoints registered by this plugin are secured using a WordPress nonce. The nonce is passed to the front-end using the `wp_localize_script` function and is stored in a global JavaScript object `ydpl` which contains the following properties:

-   `ydpl_translate_post_id`: The ID of the post to be translated.
-   `ydpl_rest_translate_url`: The URL of the API endpoint for translation requests.
-   `ydpl_supported_languages`: The list of languages supported for translation.
-   `ydpl_api_request_nonce`: The nonce used for API validation.

When making requests to the API, ensure that the nonce is included in the request headers. The header should be named `X-WP-Nonce`, and it should contain a nonce created with the `wp_rest` action (available via `ydpl.ydpl_api_request_nonce` on the front-end).

#### Rate Limiting

To prevent abuse, unauthenticated requests (or requests from users without the cache capability) are limited to **3 requests per 60 seconds** per IP address. Authenticated users with the required cache capability (default: `edit_posts`) are exempt from this rate limit.

#### Cache Capability

Only users with the `edit_posts` capability can trigger cache creation. This can be customized using the `yard::deepl/cache_capability` filter (see [Filters](#filters)).

#### Example

##### Request

```javascript
var xhr = new XMLHttpRequest();
xhr.open( 'POST', ydpl.ydpl_rest_translate_url, true );

// Set request headers
xhr.setRequestHeader( 'Content-Type', 'application/json' );
xhr.setRequestHeader( 'X-WP-Nonce', ydpl.ydpl_api_request_nonce );

// Handle response
xhr.onreadystatechange = function () {
	if ( xhr.readyState === 4 && xhr.status === 200 ) {
		console.log( 'Translation:', JSON.parse( xhr.responseText ) );
	} else if ( xhr.readyState === 4 ) {
		console.error( 'Error:', xhr.statusText );
	}
};

// Prepare and send the request body
var data = JSON.stringify( {
	text: [ 'Look another test' ],
	target_lang: 'DE',
} );

xhr.send( data );
```

##### Response

```javascript
[
	{
		text: 'Look another test!',
		translation: 'Sehen Sie sich einen weiteren Test an!',
	},
];
```

## Filters

| Filter                                             | Default        | Description                                                                                                                               |
| -------------------------------------------------- | -------------- | ----------------------------------------------------------------------------------------------------------------------------------------- |
| `yard::deepl/cache_capability`                     | `'edit_posts'` | The WordPress capability required to create cache entries. Users without this capability receive translations but results are not cached. |
| `yard::deepl/cache_metabox_post_types`             | `['page']`     | Post types on which the Yard DeepL cache metabox is displayed.                                                                            |
| ~~`yard::deepl/disable_cache_metabox_post_types`~~ | —              | **Deprecated.** Use `yard::deepl/cache_metabox_post_types` instead.                                                                       |

## Changelog

### NEXT (unreleased)

-   Add: same-origin check for REST API requests
-   Add: rate limiting for unauthenticated / low-privilege requests (3 req / 60 s per IP)
-   Add: cache creation restricted to users with `edit_posts` capability (configurable via filter)
-   Add: `yard::deepl/cache_capability` filter
-   Add: Translation cache column on post list screens showing cached languages and per-language uncached call counts, to help editors prioritise which posts to cache
-   Change: deprecated `yard::deepl/disable_cache_metabox_post_types` in favour of `yard::deepl/cache_metabox_post_types`
-   Change: nonce validation now also accepts the standard `wp_rest` nonce action as a fallback

### 1.1.0 (Jan 31, 2025)

-   Add: disable DeepL translation cache metabox
-   Change: use init hook in plugin bootstrap construct, fixes translations for WordPress 6.7

### 1.0.2 (Jan 08, 2025)

-   Change: update all occurrences of 'deepl' to 'DeepL' for consistency

### 1.0.1 (Jan 07, 2025)

-   Change: processed corrections

### 1.0.0 (Oct 18, 2024)

-   Init: first release!

## About us

[![banner](https://raw.githubusercontent.com/yardinternet/.github/refs/heads/main/profile/assets/small-banner-github.svg)](https://www.yard.nl/werken-bij/)
