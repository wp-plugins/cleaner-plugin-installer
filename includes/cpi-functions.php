<?php
/**
 * Various helper functions for this plugin.
 *
 * @package    Cleaner Plugin Installer
 * @subpackage Helper Functions
 * @author     David Decker - DECKERWEB
 * @copyright  Copyright (c) 2014, David Decker - DECKERWEB
 * @license    http://www.opensource.org/licenses/gpl-license.php GPL-2.0+
 * @link       http://genesisthemes.de/en/wp-plugins/cleaner-plugin-installer/
 * @link       http://deckerweb.de/twitter
 *
 * @since      1.0.0
 */


/**
 * Prevent direct access to this file.
 *
 * @since 1.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Sorry, you are not allowed to access this file directly.' );
}


/**
 * Helper function for returning string for minifying scripts/ stylesheets.
 *
 * @since  1.0.0
 *
 * @return string String for minifying scripts/ stylesheets.
 */
function ddw_cpi_script_suffix() {
	
	/** Bail early if not admin */
	if ( ! is_admin() ) {
		return;
	}

	return ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) ? '' : '.min';

}  // end function


/**
 * Helper function for returning string for versioning scripts/ stylesheets.
 *
 * @since  1.0.0
 *
 * @return string Version string for versioning scripts/ stylesheets.
 */
function ddw_cpi_script_version() {

	/** Bail early if not admin */
	if ( ! is_admin() ) {
		return;
	}

	return ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) ? time() : filemtime( plugin_dir_path( __FILE__ ) );

}  // end function


add_action( 'admin_init', 'ddw_cpi_register_admin_styles' );
/**
 * Load additional admin styles for our "searching" tab.
 *
 * @since 1.0.0
 *
 * @uses  wp_register_style()
 * @uses  wp_enqueue_style()
 */
function ddw_cpi_register_admin_styles() {

	/** Register the stylesheet */
	wp_register_style(
		'cpi-admin-styles',
		plugins_url( 'css/cpi-admin-styles' . ddw_cpi_script_suffix() . '.css', dirname( __FILE__ ) ),
		FALSE,
		ddw_cpi_script_version(),
		'all'
	);

}  // end function


/**
 * Helper function for building search legend output.
 *
 * @since 1.0.0
 */
function ddw_cpi_start_tab_legend() {

	return apply_filters(
		'cpi_filter_search_keys_legend',
		array(
			'keyword' => array(
				'label'       => _x( 'Keyword', 'Search filter', 'cleaner-plugin-installer' ),
				'description' => __( 'Any search term, will be searched within plugin title, description, plus readme info', 'cleaner-plugin-installer' ),
			),
			'author' => array(
				'label'       => _x( 'Author', 'Search filter', 'cleaner-plugin-installer' ),
				'description' => __( 'Plugin author (developer), will be searched in readme/ plugin header info', 'cleaner-plugin-installer' ),
			),
			'tag' => array(
				'label'       => _x( 'Tag', 'Plugin Installer, search filter', 'cleaner-plugin-installer' ),
				'description' => __( 'Plugin tag, will be searched in readme tag list (set by plugin author)', 'cleaner-plugin-installer' ),
			),
		)
	);

}  // end function


/**
 * Link building helper function for installing plugins.
 *
 * @since  1.0.0
 *
 * @param  string $plugin_slug Plugin slug.
 * @param  string $text        Plugin name.
 * @param  string $title_text  Plugin name for title attribute.
 * @param  string $class       Optional CSS class(es).
 *
 * @return string              HTML markup for links.
 */
function ddw_cpi_plugin_install_link( $plugin_slug = '', $text = '', $title_text = '', $class = '' ) {

	/** URL logic */
	if ( is_main_site() ) {

		$url = network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_slug . '&TB_iframe=true&width=600&height=550' );

	} else {

		$url = admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $plugin_slug . '&TB_iframe=true&width=600&height=550' );

	}  // end if

	/** Title attribute text */
	$title_text = sprintf(
		__( 'Install %s', 'cleaner-plugin-installer' ),
		esc_attr( $title_text )
	);

	/** Return the link markup */
	return sprintf(
		'<a%s href="%s" title="%s">%s</a>',
		( ! empty( $class ) ) ? ' class="' . esc_attr( $class ) . '"' : '',
		esc_url( $url ),
		esc_attr( $title_text ),
		wp_kses_post( $text )
	);

}  // end function