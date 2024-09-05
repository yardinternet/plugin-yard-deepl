const path = require( 'path' );

const defaultConfig = require( '@wordpress/scripts/config/webpack.config' ); // Original config from the @wordpress/scripts package.

module.exports = {
	...defaultConfig,
	entry: {
		main: [ './assets/js/translation.js' ],
	},
	output: {
		path: path.resolve( __dirname, 'dist' ),
	},
};
