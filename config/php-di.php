<?php

/**
 * PHP DI.
 *
 * @package Yard_Deepl
 * @author  Yard | Digital Agency
 * @since   0.0.1
 */

/**
 * Exit when accessed directly.
 */

if ( ! defined( 'ABSPATH' )) {
	exit;
}

return array(
	'ydpl.blade_compiler'             => YardDeepl\Singletons\BladeSingleton::getInstance(),
	'ydpl.site_options'               => YardDeepl\Singletons\SiteOptionsSingleton::getInstance( get_option( YDPL_SITE_OPTION_NAME, array() ) ),
	'ydpl.supported_target.languages' => array(
		array(
			'iso_alpha2' => 'AR',
			'name'       => __( 'Arabic', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'BG',
			'name'       => __( 'Bulgarian', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'CS',
			'name'       => __( 'Czech', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'DA',
			'name'       => __( 'Danish', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'DE',
			'name'       => __( 'German', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'EL',
			'name'       => __( 'Greek', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'EN-GB',
			'name'       => __( 'English (British)', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'EN-US',
			'name'       => __( 'English (American)', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'ES',
			'name'       => __( 'Spanish', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'ET',
			'name'       => __( 'Estonian', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'FI',
			'name'       => __( 'Finnish', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'FR',
			'name'       => __( 'French', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'HU',
			'name'       => __( 'Hungarian', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'ID',
			'name'       => __( 'Indonesian', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'IT',
			'name'       => __( 'Italian', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'JA',
			'name'       => __( 'Japanese', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'KO',
			'name'       => __( 'Korean', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'LT',
			'name'       => __( 'Lithuanian', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'LV',
			'name'       => __( 'Latvian', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'NB',
			'name'       => __( 'Norwegian Bokmål', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'NL',
			'name'       => __( 'Dutch', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'PL',
			'name'       => __( 'Polish', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'PT-BR',
			'name'       => __( 'Portuguese (Brazilian)', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'PT-PT',
			'name'       => __( 'Portuguese (Portugal)', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'RO',
			'name'       => __( 'Romanian', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'RU',
			'name'       => __( 'Russian', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'SK',
			'name'       => __( 'Slovak', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'SL',
			'name'       => __( 'Slovenian', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'SV',
			'name'       => __( 'Swedish', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'TR',
			'name'       => __( 'Turkish', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'UK',
			'name'       => __( 'Ukrainian', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'ZH-HANS',
			'name'       => __( 'Chinese (Simplified)', 'yard-deepl' ),
		),
		array(
			'iso_alpha2' => 'ZH-HANT',
			'name'       => __( 'Chinese (Traditional)', 'yard-deepl' ),
		),
	),
);
