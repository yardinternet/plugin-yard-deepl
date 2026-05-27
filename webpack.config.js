const path = require( 'path' );

const defaultConfig = require( '@wordpress/scripts/config/webpack.config' ); // Original config from the @wordpress/scripts package.

module.exports = {
	...defaultConfig,
	entry: {
		'admin-columns': [ './assets/css/admin-columns.css' ],
		editor: [ './assets/css/editor.css' ],
		main: [ './assets/js/translation.js' ],
	},
	output: {
		path: path.resolve( __dirname, 'dist' ),
	},
};
