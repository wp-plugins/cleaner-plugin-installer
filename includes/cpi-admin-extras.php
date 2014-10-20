<?php
/**
 * Helper functions for the admin - plugin links and help tabs.
 *
 * @package    Cleaner Plugin Installer
 * @subpackage Admin
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
 * Setting internal plugin helper values.
 *
 * @since  1.0.0
 *
 * @uses   get_locale()
 *
 * @return array $cpi_info Array of info values.
 */
function ddw_cpi_info_values() {

	$cpi_info = array(

		'url_translate'     => 'http://translate.wpautobahn.com/projects/wordpress-plugins-deckerweb/cleaner-plugin-installer',
		'url_wporg_faq'     => 'https://wordpress.org/plugins/cleaner-plugin-installer/faq/',
		'url_wporg_forum'   => 'https://wordpress.org/support/plugin/cleaner-plugin-installer',
		'url_wporg_profile' => 'https://profiles.wordpress.org/daveshine/',
		'url_snippets'      => 'https://gist.github.com/deckerweb/8e3bc0a1d62a096695db',
		'license'           => 'GPL-2.0+',
		'url_license'       => 'http://www.opensource.org/licenses/gpl-license.php',
		'first_release'     => absint( '2014' ),
		'url_donate'        => ( in_array( get_locale(), array( 'de_DE', 'de_AT', 'de_CH', 'de_LU', 'gsw' ) ) ) ? 'http://genesisthemes.de/spenden/' : 'http://genesisthemes.de/en/donate/',
		'url_plugin'        => ( in_array( get_locale(), array( 'de_DE', 'de_AT', 'de_CH', 'de_LU', 'gsw' ) ) ) ? 'http://genesisthemes.de/plugins/cleaner-plugin-installer/' : 'http://genesisthemes.de/en/wp-plugins/cleaner-plugin-installer/'

	);  // end of array

	return $cpi_info;

}  // end of function ddw_cpi_info_values


/**
 * Add "Plugin Install" link to plugin page.
 *
 * @since  1.0.0
 *
 * @param  $cpi_links
 * @param  $cpi_widgets_link
 *
 * @return strings $cpi_links String of Plugins admin link.
 */
function ddw_cpi_plugins_page_link( $cpi_links ) {

	/** Plugin Install Admin link */
	$cpi_plugins_link = sprintf(
		'<a href="%s" title="%s">%s</a>',
		network_admin_url( 'plugin-install.php?tab=featured' ),
		esc_html__( 'Go to the Plugin Installer page', 'cleaner-plugin-installer' ),
		esc_attr__( 'Plugin Installer', 'cleaner-plugin-installer' )
	);

	/** Set the order of the links */
	array_unshift( $cpi_links, $cpi_plugins_link );

	/** Display plugin settings links */
	return apply_filters( 'cpi_filter_settings_page_link', $cpi_links );

}  // end function


add_filter( 'plugin_row_meta', 'ddw_cpi_plugin_links', 10, 2 );
/**
 * Add various support links to plugin page.
 *
 * @since  1.0.0
 *
 * @uses   ddw_cpi_info_values()
 *
 * @param  $cpi_links
 * @param  $cpi_file
 *
 * @return string $cpi_links String of plugin links.
 */
function ddw_cpi_plugin_links( $cpi_links, $cpi_file ) {

	/** Capability check */
	if ( ! current_user_can( 'install_plugins' ) ) {

		return $cpi_links;

	}  // end if cap check

	/** List additional links only for this plugin */
	if ( $cpi_file == CLPINST_PLUGIN_BASEDIR . 'cleaner-plugin-installer.php' ) {

		$cpi_info = (array) ddw_cpi_info_values();

		$cpi_links[] = '<a href="' . esc_url( $cpi_info[ 'url_wporg_faq' ] ) . '" target="_new" title="' . __( 'FAQ', 'cleaner-plugin-installer' ) . '">' . __( 'FAQ', 'cleaner-plugin-installer' ) . '</a>';

		$cpi_links[] = '<a href="' . esc_url( $cpi_info[ 'url_wporg_forum' ] ) . '" target="_new" title="' . __( 'Support', 'cleaner-plugin-installer' ) . '">' . __( 'Support', 'cleaner-plugin-installer' ) . '</a>';

		$cpi_links[] = '<a href="' . esc_url( $cpi_info[ 'url_snippets' ] ) . '" target="_new" title="' . __( 'Code Snippets for Customization', 'cleaner-plugin-installer' ) . '">' . __( 'Code Snippets', 'cleaner-plugin-installer' ) . '</a>';

		$cpi_links[] = '<a href="' . esc_url( $cpi_info[ 'url_translate' ] ) . '" target="_new" title="' . __( 'Translations', 'cleaner-plugin-installer' ) . '">' . __( 'Translations', 'cleaner-plugin-installer' ) . '</a>';

		$cpi_links[] = '<a href="' . esc_url( $cpi_info[ 'url_donate' ] ) . '" target="_new" title="' . __( 'Donate', 'cleaner-plugin-installer' ) . '"><strong>' . __( 'Donate', 'cleaner-plugin-installer' ) . '</strong></a>';

	}  // end if plugin links

	/** Output the links */
	return apply_filters( 'cpi_filter_plugin_links', $cpi_links );

}  // end function


add_action( 'admin_head-plugin-install.php', 'ddw_cpi_plugin_installer_help_tab', 20 );
/**
 * Create and display plugin help tab.
 *
 * @since  1.0.0
 *
 * @uses   get_current_screen()
 * @uses   get_template_directory()
 * @uses   WP_Screen::add_help_tab
 *
 * @global mixed $GLOBALS[ 'cpi_plugins_screen' ], $GLOBALS[ 'tab' ]
 */
function ddw_cpi_plugin_installer_help_tab() {

	$GLOBALS[ 'cpi_plugins_screen' ] = get_current_screen();

	/** Display help tabs only for WordPress 3.3 or higher */
	if ( ! class_exists( 'WP_Screen' )
		|| ! $GLOBALS[ 'cpi_plugins_screen' ]
		|| ( ! in_array( $GLOBALS[ 'tab' ], array( 'featured', 'topics', 'collections' ) ) )
	) {

		return;

	}  // end if

	/** Add the new help tab */
	$GLOBALS[ 'cpi_plugins_screen' ]->add_help_tab( array(
		'id'       => 'cpi-plugin-installer-help',
		'title'    => __( 'Cleaner Plugin Installer', 'cleaner-plugin-installer' ),
		'callback' => apply_filters( 'cpi_filter_help_tab_content', 'ddw_cpi_plugin_installer_help_content' ),
	) );

}  // end function


/**
 * Create and display plugin help tab content.
 *
 * @since 1.0.0
 *
 * @uses  ddw_cpi_info_values() To get some strings of info values.
 * @uses  ddw_cpi_plugin_get_data() To display various data of this plugin.
 * @uses  ddw_cpi_topics_tab_content_disclaimer() To output disclaimer content.
 * @uses  ddw_cpi_collections_tab_content_disclaimer() To output disclaimer content.
 */
function ddw_cpi_plugin_installer_help_content() {

	$cpi_info = (array) ddw_cpi_info_values();

	$cpi_space_helper = '<div style="height: 5px;"></div>';

	/** Headline */
	echo '<h3>' . __( 'Plugin', 'cleaner-plugin-installer' ) . ': ' . __( 'Cleaner Plugin Installer', 'cleaner-plugin-installer' ) . ' <small>v' . esc_attr( ddw_cpi_plugin_get_data( 'Version' ) ) . '</small></h3>';

	/** Start Search tab */
	echo '<h4>' . _x( 'Start Search Tab', 'Help tab headline', 'cleaner-plugin-installer' ) . ':</h4>';
	echo '<p class="description"><strong>' . __( 'Legend', 'cleaner-plugin-installer' ) . ':</strong></p> ';

	echo '<div class="cpi-block">';
	foreach ( ddw_cpi_start_tab_legend() as $string => $string_item ) {

		$output = sprintf(
			'<p class="description"><strong>%s &rarr;</strong> %s</p>',
			$string_item[ 'label' ],
			$string_item[ 'description' ]
		);
	
		echo $output;

	}  // end foreach
	echo '</div>';

	/** Topics tab */
	echo '<h4>' . _x( 'Topics Tab', 'Help tab headline', 'cleaner-plugin-installer' ) . ':</h4>';
	echo '<p class="description"><strong>' . __( 'Please Note', 'cleaner-plugin-installer' ) . ':</strong></p> ';
	echo ddw_cpi_topics_tab_content_disclaimer( FALSE );

	/** Collections tab */
	echo '<h4>' . _x( 'Collections Tab', 'Help tab headline', 'cleaner-plugin-installer' ) . ':</h4>';
	echo '<p class="description">' . sprintf(
		__( '%s is an external third-party service.', 'cleaner-plugin-installer' ),
		'<a href="http://ddwb.me/wpcore" target="_blank">' . __( 'WPCore Plugin Manager', 'cleaner-plugin-installer' ) . '</a>'
	) . '</p>';
	echo ddw_cpi_collections_tab_content_disclaimer(
		__( 'Disclaimer', 'cleaner-plugin-installer' ) . ':',
		FALSE
	);

	/** Help footer: plugin info */
	echo $cpi_space_helper . '<p><h4>' . __( 'Important plugin links:', 'cleaner-plugin-installer' ) . '</h4>' .
	
	'<a class="button" href="' . esc_url( $cpi_info[ 'url_plugin' ] ) . '" target="_new" title="' . __( 'Plugin website', 'cleaner-plugin-installer' ) . '">' . __( 'Plugin website', 'cleaner-plugin-installer' ) . '</a>' .

	'&nbsp;&nbsp;<a class="button" href="' . esc_url( $cpi_info[ 'url_wporg_faq' ] ) . '" target="_new" title="' . __( 'FAQ', 'cleaner-plugin-installer' ) . '">' . __( 'FAQ', 'cleaner-plugin-installer' ) . '</a>' .

	'&nbsp;&nbsp;<a class="button" href="' . esc_url( $cpi_info[ 'url_wporg_forum' ] ) . '" target="_new" title="' . __( 'Support', 'cleaner-plugin-installer' ) . '">' . __( 'Support', 'cleaner-plugin-installer' ) . '</a>' .

	'&nbsp;&nbsp;<a class="button" href="' . esc_url( $cpi_info[ 'url_snippets' ] ) . '" target="_new" title="' . __( 'Code Snippets for Customization', 'cleaner-plugin-installer' ) . '">' . __( 'Code Snippets', 'cleaner-plugin-installer' ) . '</a>' .

	'&nbsp;&nbsp;<a class="button" href="' . esc_url( $cpi_info[ 'url_translate' ] ) . '" target="_new" title="' . __( 'Translations', 'cleaner-plugin-installer' ) . '">' . __( 'Translations', 'cleaner-plugin-installer' ) . '</a>' .

	'&nbsp;&nbsp;<a class="button" href="' . esc_url( $cpi_info[ 'url_donate' ] ) . '" target="_new" title="' . __( 'Donate', 'cleaner-plugin-installer' ) . '"><strong>' . __( 'Donate', 'cleaner-plugin-installer' ) . '</strong></a></p>';

	/** Set first release year */
	$release_first_year = ( '' != $cpi_info[ 'first_release' ] && date( 'Y' ) != $cpi_info[ 'first_release' ] ) ? $cpi_info[ 'first_release' ] . '&#x02013;' : '';
	
	echo '<p><a href="' . esc_url( $cpi_info[ 'url_license' ] ). '" target="_new" title="' . esc_attr( $cpi_info[ 'license' ] ). '">' . esc_attr( $cpi_info[ 'license' ] ). '</a> &#x000A9; ' . $release_first_year . date( 'Y' ) . ' <a href="' . esc_url( ddw_cpi_plugin_get_data( 'AuthorURI' ) ) . '" target="_new" title="' . esc_attr__( ddw_cpi_plugin_get_data( 'Author' ) ) . '">' . esc_attr__( ddw_cpi_plugin_get_data( 'Author' ) ) . '</a></p>';

}  // end function