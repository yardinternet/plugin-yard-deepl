# Yard Deepl

Author: Yard Digital Agency
Author URI: <https://www.yard.nl>
Contributors: yarddigitalagency
License: GPLv2 or later
License URI: <https://www.gnu.org/licenses/gpl-2.0.html>
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 1.0.0
Tags: deepl, translating, secure
Tested up to: 6.6.2

This plugin registers secure API endpoints that allow you to request translations directly from DeepL without exposing your Deepl API-key.

## Description

This plugin registers secure API endpoints that allow you to request translations directly from DeepL without exposing your Deepl API-key. These endpoints are only accessible when a valid nonce is provided.
When providing translations to website visitors, you can configure which languages are supported for translation.

## Caching Mechanism

Each object that is translated will store its cached translation in the `wp_postmeta` table within the database. This caching mechanism ensures that translations are efficiently reused, reducing unnecessary API requests to DeepL and saving costs.

- **Serving Cached Translations:** If a cached translation is newer than the `post_modified` date of the object, the cached version is served.
- **Fetching New Translations:** When the `post_modified` date of the object is more recent than the cached translation, a new translation is fetched from DeepL. Once retrieved, this translation is immediately cached for future use.

This approach minimizes the number of API calls to DeepL, ensuring translations are kept up to date only when necessary.

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

- `ydpl_translate_post_id`: The ID of the post to be translated.
- `ydpl_rest_translate_url`: The URL of the API endpoint for translation requests.
- `ydpl_supported_languages`: The list of languages supported for translation.
- `ydpl_api_request_nonce`: The nonce used for API validation.

When making requests to the API, ensure that the nonce is included in the request headers. The header should be named `nonce`, and it should contain the value of `ydpl_api_request_nonce`.

#### Example

##### Request

```javascript
var xhr = new XMLHttpRequest();
xhr.open('POST', ydpl.ydpl_rest_translate_url, true);

// Set request headers
xhr.setRequestHeader('Content-Type', 'application/json');
xhr.setRequestHeader('nonce', ydpl.ydpl_api_request_nonce);

// Handle response
xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
        console.log('Translation:', JSON.parse(xhr.responseText));
    } else if (xhr.readyState === 4) {
        console.error('Error:', xhr.statusText);
    }
};

// Prepare and send the request body
var data = JSON.stringify({
    text: ["Look another test"],
    target_lang: "DE"
});

xhr.send(data);
```

##### Response

```javascript
[
    {
        "text": "Look another test!",
        "translation": "Sehen Sie sich einen weiteren Test an!"
    }
]
```
