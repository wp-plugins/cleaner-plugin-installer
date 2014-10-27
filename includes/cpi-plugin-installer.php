<?php
/**
 * Basic functionality of this plugin.
 *
 * @package    Cleaner Plugin Installer
 * @subpackage Admin Plugin Installer
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


add_filter( 'install_plugins_tabs', 'ddw_cpi_plugin_installer_tweak_tabs', 10, 1 );
/**
 * Plugin installer tabs: Rename, reorder some existing tabs, plus, add new tabs
 *    to the stack.
 *
 * @since  1.0.0
 *
 * @uses   wp_enqueue_style() Enqueues our style.
 * @uses   current_user_can() Checking user capabilities.
 * @uses   is_rtl()           Detecting Right-To-Left languages.
 *
 * @param  array $tabs Array of plugin installer tabs.
 *
 * @global string $GLOBALS[ 'tab' ]
 * @global string $GLOBALS[ 'wp_version' ]
 */
function ddw_cpi_plugin_installer_tweak_tabs( $tabs ) {

	/** Enqueue the stylesheet */
	wp_enqueue_style( 'cpi-admin-styles' );

	/** Enqueue optional RTL stylesheet */
	if ( is_rtl() ) {

		wp_enqueue_style( 'cpi-admin-styles-rtl' );

	}  // end if

	$tabs = array();

	/** Rename existing tab */
	$tabs[ 'featured' ] = '<strong>' . _x( 'Start: Search', 'Tab name in toolbar', 'cleaner-plugin-installer' ) . '</strong>';

	/** Add new "Topics" tab */
	$tabs[ 'topics' ] = '<strong>' . _x( 'Topics', 'Tab name in toolbar', 'cleaner-plugin-installer' ) . '</strong>';

	/** Optional include "Collections"/ "WPCore" plugin info */
	if ( current_user_can( 'manage_options' ) ) {

		$tabs[ 'collections' ] = '<strong>' . _x( 'Collections', 'Tab name in toolbar', 'cleaner-plugin-installer' ) . '</strong>';

	}  // end if

	/** Re-enable hidden tab from core */
	$tabs[ 'new' ] = _x( 'Newest', 'Tab name in toolbar', 'cleaner-plugin-installer' );

	/** Stack other existings tabs to the end */
	$tabs_later                = array();
	$tabs_later[ 'popular' ]   = _x( 'Popular', 'Tab name in toolbar', 'cleaner-plugin-installer' );
	$tabs_later[ 'favorites' ] = _x( 'Favorites', 'Tab name in toolbar', 'cleaner-plugin-installer' );
	/** For alpha-, beta-, RC-versions of WordPress Core */
	if ( 'beta' === $GLOBALS[ 'tab' ] || false !== strpos( $GLOBALS[ 'wp_version' ], '-' ) ) {
		$tabs_later[ 'beta' ]  = _x( 'Beta Testing', 'Tab name in toolbar', 'cleaner-plugin-installer' );
	}
	/** Include search tab here, only when performing a search */
	if ( 'search' == $GLOBALS[ 'tab' ] ) {
		$tabs_later[ 'search' ] = _x( 'Search Results', 'Tab name in toolbar', 'cleaner-plugin-installer' );
	}
	/** The 'virtual tab' "Upload Plugin" (button) */
	if ( current_user_can( 'upload_plugins' ) ) {
		$tabs_later[ 'upload' ] = _x( 'Upload Plugin', 'Upload button label', 'cleaner-plugin-installer' );
	}

	/** Merge our "default" tabs with the new "add-to-the-end-of-stack" tabs */
	$tabs = array_merge(
		apply_filters( 'cpi_filter_install_plugins_tabs', $tabs ),
		apply_filters( 'cpi_filter_install_plugins_tabs_later', $tabs_later )
	);

	/** Return tweaked tabs array */
	return $tabs;

}  // end function


add_action( 'install_plugins_pre_featured', 'ddw_cpi_tweak_plugin_installer_start', 0 );
/**
 * Remove featured content.
 *    Instead add our own content.
 *
 * @since 1.0.0
 */
function ddw_cpi_tweak_plugin_installer_start() {

	/** Remove original "featured" tab/ content */
	remove_action( 'install_plugins_featured', 'install_dashboard' );		// WP 4.0+
	remove_action( 'install_plugins_featured', 'display_plugins_table' );	// WP pre 4.0

	/** Load our admin body class functionality */
	add_filter( 'admin_body_class', 'ddw_cpi_add_admin_body_class' );

	/** Load our new stripped down content */
	add_action( 'install_plugins_featured', 'ddw_cpi_plugins_install_dashboard' );

}  // end function


/**
 * Set our own action hook: for adding various content blocks easily.
 *
 * @since 1.0.0
 */
function ddw_cpi_plugins_install_dashboard() {

	do_action( 'ddw_cpi_plugin_installer_start' );

}  // end function


add_action( 'ddw_cpi_plugin_installer_start', 'ddw_cpi_start_tab_text_intro' );
/**
 * Tab "Start: Search": Intro text. GPL-2.0+
 *
 * @since 1.0.0
 *
 * @uses  ddw_cpi_info_values()
 */
function ddw_cpi_start_tab_text_intro() {

	$cpi_info = (array) ddw_cpi_info_values();

	$output = '
		<div class="cpi-block cpi-start-intro">
			<h3>' . __( 'Search for Plugins', 'cleaner-plugin-installer' ) . '</h3>
			<p>' . sprintf(
				/* Translators: WordPress.org repo */
				__( 'Find plugins on the official %s plugin repository.', 'cleaner-plugin-installer' ),
				__( 'WordPress.org', 'cleaner-plugin-installer' )
			) . ' ' . sprintf(
				/* Translators: 1 = Open Source, 2 = GPL-2.0+ */
				__( 'All are %s and licensed under %s or compatible.', 'cleaner-plugin-installer' ),
				'<a href="' . network_admin_url( 'freedoms.php' ) . '">' . __( 'Open Source', 'cleaner-plugin-installer' ) . '</a>',
				'<a href="' . esc_url( $cpi_info[ 'url_license' ] ). '" target="_new">' . esc_attr( $cpi_info[ 'license' ] ). '</a>'
			) . '</p><br class="clear" />
		</div>
	';

	echo $output;

}  // end function


add_action( 'ddw_cpi_plugin_installer_start', 'ddw_cpi_start_tab_search_field' );
/**
 * Tab "Start: Search": Large search input field.
 *
 * NOTE:
 *  This search form is pretty much a copy of the original search from Core!
 *  (See function install_search_form() in wp-admin/includes/plugin-install.php)
 *
 * @since 1.0.0
 */
function ddw_cpi_start_tab_search_field() {

	$type        = isset( $_REQUEST[ 'type' ] ) ? wp_unslash( $_REQUEST[ 'type' ] ) : 'term';
	$term        = isset( $_REQUEST[ 's' ] ) ? wp_unslash( $_REQUEST[ 's' ] ) : '';
	$input_attrs = '';
	$button_type = apply_filters( 'cpi_filter_start_button_type', 'primary' );

	?>
	<div class="cpi-block cpi-start-search-form">
		<form class="cpi-search-form search-plugins" method="get" action="">
			<input type="hidden" name="tab" value="search" />

			<div class="one-sixth first">
				<select name="type" id="typeselector">
					<option value="term"<?php selected( 'term', $type ) ?>><?php _ex( 'Keyword', 'Search filter', 'cleaner-plugin-installer' ); ?>:</option>
					<option value="author"<?php selected( 'author', $type ) ?>><?php _ex( 'Author', 'Search filter', 'cleaner-plugin-installer' ); ?>:</option>
					<option value="tag"<?php selected( 'tag', $type ) ?>><?php _ex( 'Tag', 'Plugin Installer, search filter', 'cleaner-plugin-installer' ); ?>:</option>
				</select>
			</div>

			<div class="three-sixths">
				<label><span class="screen-reader-text"><?php _ex( 'Search Plugins', 'Screen reader text', 'cleaner-plugin-installer' ); ?></span>
					<input type="search" name="s" placeholder="<?php _ex( 'Your plugin search term &hellip;', 'Placeholder label', 'cleaner-plugin-installer' ); ?>" value="<?php echo esc_attr( $term ) ?>" <?php echo $input_attrs; ?>/>
				</label>
			</div>

			<div class="two-sixths">
				<?php submit_button(
					_x( 'Search Plugins', 'Submit button label', 'cleaner-plugin-installer' ),
					sanitize_html_class( $button_type ),
					FALSE,
					FALSE,
					array( 'id' => 'search-submit' )
				); ?>
			</div>
			<div class="clear"></div>
		</form>
	</div>
	<?php

}  // end function


add_action( 'ddw_cpi_plugin_installer_start', 'ddw_cpi_start_tab_text_after' );
/**
 * Tab "Start: Search": Outro text (legend).
 *
 * @since 1.0.0
 *
 * @uses  ddw_cpi_start_tab_legend() To build the legend strings & markup.
 */
function ddw_cpi_start_tab_text_after() {

	/** Build our output for display */
	$output = '<div class="cpi-block cpi-start-after"><br /><hr /><br />';

	/** Hook in after <hr /> but within our div containers */
	do_action( 'ddw_cpi_start_tab_legend_before' );

	/** Conditionally show our legend user notes */
	if ( apply_filters( 'cpi_filter_show_start_tab_legend', TRUE ) ) {

		foreach ( ddw_cpi_start_tab_legend() as $string => $string_item ) {

			$output .= sprintf(
				'<div class="one-sixth first description"><strong>%s <span class="cpi-arrow">%s</span></strong></div><div class="five-sixths description">%s</div>',
				$string_item[ 'label' ],
				( ! is_rtl() ) ? '&rarr;' : '&larr;',
				$string_item[ 'description' ]
			);

		}  // end foreach

	}  // end if

	echo '<div class="clear"></div>';

	/** Hook in after legend content but still within our div containers */
	do_action( 'ddw_cpi_start_tab_legend_after' );

	$output .= '</div>';

	/** Render the output */
	echo $output;

}  // end function


add_action( 'install_plugins_topics', 'ddw_cpi_installer_tab_topics' );
/**
 * Add special tab "Topics" (lists of curated tags).
 *    Set our own action hook for easily addting content.
 *    Load the required helper functions for this tab only.
 *
 * @since 1.0.0
 */
function ddw_cpi_installer_tab_topics() {

	/** Include admin helper functions */
	require_once( CLPINST_PLUGIN_DIR . 'includes/cpi-functions-tab-topics.php' );

	if ( in_array( get_locale(), array( 'de_DE', 'de_AT', 'de_CH', 'de_LU', 'gsw' ) ) ) {

		require_once( CLPINST_PLUGIN_DIR . 'includes/cpi-functions-de.php' );

	}  // end if

	do_action( 'ddw_cpi_plugin_installer_topics' );

}  // end function


add_action( 'install_plugins_pre_topics', 'ddw_cpi_prepare_tabs' );
add_action( 'install_plugins_pre_collections', 'ddw_cpi_prepare_tabs' );
/**
 * Tab "Topics" & Tab "Collections": add our body class.
 *
 * @since 1.0.0
 */
function ddw_cpi_prepare_tabs() {

	/** Load our admin body class functionality */
	add_filter( 'admin_body_class', 'ddw_cpi_add_admin_body_class' );

}  // end function


add_action( 'ddw_cpi_plugin_installer_topics', 'ddw_cpi_topics_tab_content' );
/**
 * Build the "Topics" tab content.
 *    Includes all default lists by this plugin, and their ? functions.
 *    See plugin file "cpi-functions-tab-topics.php" for all topic functions.
 *
 * @since 1.0.0
 *
 * @uses  ddw_cpi_topic_{content-type}() Render title for tag lists.
 * @uses  ddw_cpi_topics_tab_content_disclaimer()
 */
function ddw_cpi_topics_tab_content() {

	/** Load our admin body class functionality */
	add_filter( 'admin_body_class', 'ddw_cpi_add_admin_body_class' );

	/** All collections, filterable */
	$collections = apply_filters(
		'cpi_filter_topic_tag_collections',
		array(
			'content_editor' => array(
				'title'    => _x( 'Content/ Editor', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_content_editor(),
			),
			'post_types' => array(
				'title'    => _x( 'Post Types/ Custom Taxonomies/ Custom Fields', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_post_types(),
			),
			'media' => array(
				'title'    => _x( 'Media/ Gallery/ Video', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_media(),
			),
			'tools' => array(
				'title'    => _x( 'Tools, Helpers', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_tools(),
			),
			'multisite' => array(
				'title'    => _x( 'Multisite/ Network', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_multisite(),
			),
			'ecommerce' => array(
				'title'    => _x( 'E-Commerce/ Shop Plugins/ Digital Products', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_ecommerce(),
			),
			'forms' => array(
				'title'    => _x( 'Contact Forms/ Form Builders', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_forms(),
			),
			'community_forum' => array(
				'title'    => _x( 'Community/ Forum', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_community_forum(),
			),
			'social' => array(
				'title'    => _x( 'Social/ Sharing', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_social(),
			),
			'membership' => array(
				'title'    => _x( 'Membership/ Restrict Content', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_membership(),
			),
			'multilingual' => array(
				'title'    => _x( 'Multilingual/ Translations', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_multilingual(),
			),
			'security_backup' => array(
				'title'    => _x( 'Security/ Backup', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_security_backup(),
			),
			'themes' => array(
				'title'    => _x( 'Themes', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_themes(),
			),
			'developer' => array(
				'title'    => _x( 'Developer', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_developer(),
			),
			'special_tags' => array(
				'title'    => _x( 'Special Tags', 'Topic tags collection title', 'cleaner-plugin-installer' ),
				'function' => ddw_cpi_topic_special_tags(),
			),
		)
	);

	echo '<div class="cpi-block cpi-tab-topics">';
		echo '<div class="cpi-topics-intro">';
		echo '<h4 class="description">' . __( 'Curated List of Tags for Lots of Plugin Topics, Use Cases etc.', 'cleaner-plugin-installer' ) . '</h4>';
			ddw_cpi_topics_tab_content_disclaimer();
		echo '</div>';

	echo '<div class="cpi-topics-group">';

	/** Finally render our topic tags collections list */
	foreach ( (array) $collections as $collection => $collection_args ) {

		/** Sets the type of search for each list, fallback to "tag" if empty */
		$type = ( isset( $collection_args[ 'type' ] ) && ! empty( $collection_args[ 'type' ] ) ) ? sanitize_key( $collection_args[ 'type' ] ) : 'tag';

		/** Further sanitizing the search type, only 3 alternatives possible! */
		$type = ( in_array( $type, array( 'term', 'author', 'tag' ) ) ) ? $type : 'tag';

		echo ddw_cpi_render_topic_tags(
			$collection_args[ 'title' ],
			$collection_args[ 'function' ],
			$type
		);

	}  // end foreach

	echo '</div>';

	echo '</div>';

}  // end function


add_action( 'install_plugins_collections', 'ddw_cpi_installer_tab_collections' );
/**
 * Add special tab "Collections" to display info and action links for
 *    "WPCore Plugin Manager" base plugin (third-party).
 *    Set our own action hook for easily addting content.
 *
 * @since 1.0.0
 */
function ddw_cpi_installer_tab_collections() {

	do_action( 'ddw_cpi_plugin_installer_collections' );

}  // end function


add_action( 'ddw_cpi_plugin_installer_collections', 'ddw_cpi_collections_tab_content' );
/**
 * Build content for Collections tab.
 *    Conditionally for active/ inactive "WPCore" plugin.
 *
 * @since 1.0.0
 *
 * @uses  ddw_cpi_plugin_install_link()
 * @uses  ddw_cpi_collections_tab_content_disclaimer()
 */
function ddw_cpi_collections_tab_content() {

	if ( ! class_exists( 'WPCore' ) ) {

		add_action( 'admin_enqueue_scripts', 'add_thickbox' );

		echo '<div class="cpi-block cpi-tab-collections">';
			echo '<h3>' . __( 'Would You Like Having Your Own Plugin Collections, plus Bulk Installing?', 'cleaner-plugin-installer' ) . '</h3>';
			echo '<p><strong>' . __( '&hellip; so are we!', 'cleaner-plugin-installer' ) . ' ;-)</strong></p>';

			/** First column */
			echo '<div class="one-half first">';

				echo '<p>' . __( 'Free and GPL-licensed third-party plugin "WPCore Plugin Manager" just manages to do this.', 'cleaner-plugin-installer' ) . '</p>';

				echo '<p>' . __( 'Follow these 2 steps to use it', 'cleaner-plugin-installer' ) . ':</p>';
				echo '<p><a class="button-primary" href="http://ddwb.me/wpcore" target="_new"><strong>&rarr;</strong> ' . __( 'Create Collections at WPCore.com', 'cleaner-plugin-installer' ) . '</a><br />' . __( 'Create a 1 or more collections (private and/ or public)', 'cleaner-plugin-installer' ) . '</p>';
				echo '<p>' . ddw_cpi_plugin_install_link(
					'wpcore',
					'<strong>&rarr;</strong> ' . __( 'Install + activate the WPCore Plugin Manager', 'cleaner-plugin-installer' ),
					__( 'WPCore', 'cleaner-plugin-installer' ),
					'button-primary thickbox'
				) . '</p>';

			echo '</div>';

			/** Second column */
			echo '<div class="one-half">';

				ddw_cpi_collections_tab_content_disclaimer(
					__( 'Please Note', 'cleaner-plugin-installer' )
				);

			echo '</div>';
			echo '<div class="clear"></div>';
		echo '</div>';

	} else {

		echo '<div class="cpi-block cpi-tab-collections">';
			echo '<h3>' . __( 'WPCore Plugin Collections, plus Bulk Installing', 'cleaner-plugin-installer' ) . '</h3>';
			echo '<p>' . __( 'Manage Your favorite plugins more easily and efficiently!', 'cleaner-plugin-installer' ) . '</p>';

			/** First column */
			echo '<div class="one-half first">';

				echo '<p><a class="button-primary" href="' . admin_url( 'admin.php?page=wpcore' ) . '"><strong>&rarr;</strong> '. __( 'Install plugins from Your collections', 'cleaner-plugin-installer' ) . '</a><br />' . __( 'plus, connect/ remove collections', 'cleaner-plugin-installer' ) . '</p>';
				echo '<p><a class="button-primary" href="https://wpcore.com/yourcollections" target="_new"><strong>&rarr;</strong> ' . __( 'Manage Your private & public plugin collections', 'cleaner-plugin-installer' ) . '</a><br />' . __( 'via WPCore.com', 'cleaner-plugin-installer' ) . '</p>';

			echo '</div>';

			/** Second column */
			echo '<div class="one-half">';

				ddw_cpi_collections_tab_content_disclaimer(
					__( 'Please Note', 'cleaner-plugin-installer' )
				);

			echo '</div>';
			echo '<div class="clear"></div>';
		echo '</div>';

	}  // end if

}  // end function


/**
 * Helper function for displaying Topics tab disclaimer content.
 *
 * @since  1.0.0
 *
 * @param  bool $echo For echoing or returning the output.
 *
 * @return string $output Echoing or returing string with markup.
 */
function ddw_cpi_topics_tab_content_disclaimer( $echo = TRUE ) {

	$output = '<p class="description">' . sprintf(
			__( 'Manually curated tag lists for a lot of plugin types, use cases and popular plugin solutions. Listing here maintained by David Decker (plugin author of %s). The tags itself are from plugin\'s readme header.', 'cleaner-plugin-installer' ),
			'"' . __( 'Cleaner Plugin Installer', 'cleaner-plugin-installer' ) . '"'
		) . '</p>';

	/** Render/ return output */
	if ( $echo ) {

		echo $output;

	} else {

		return $output;

	}  // end if

}  // end function


/**
 * Helper function for displaying Collections tab disclaimer content.
 *
 * @since  1.0.0
 *
 * @param  string $title Title string for section.
 * @param  bool   $echo  For echoing or returning the output.
 *
 * @return string $output Echoing or returing string with markup.
 */
function ddw_cpi_collections_tab_content_disclaimer( $title = '', $echo = TRUE ) {

	/** Build output */
	$output = '<p class="description"><strong>' . esc_attr( $title ) . '</strong></p>';
	$output .= '<p class="description">&raquo;' . __( 'I am NOT affiliated with "WPCore" in any way, other than that, being a normal user of their service & plugin. "WPCore" is a community effort aiming to make some things a bit easier for a lot of administrators and webmasters.', 'cleaner-plugin-installer' ) . '&laquo;</p>';
	$output .= '<p class="description"><cite>&mdash; ' . sprintf(
			__( 'David Decker, author of %s plugin', 'cleaner-plugin-installer' ),
			'&#x0022;' . __( 'Cleaner Plugin Installer', 'cleaner-plugin-installer' ) . '&#x0022;'
		) . '</cite></p>';

	/** Render/ return output */
	if ( $echo ) {

		echo $output;

	} else {

		return $output;

	}  // end if

}  // end function


add_action( 'load-plugin-install.php', 'ddw_cpi_add_plugin_installer_screen_options' );
 /**
  * Add and register our screen option for plugin cards per page.
  *
  * @since  1.0.0
  *
  * @uses   add_screen_option()
  *
  * @global string $GLOBALS[ 'wp_version' ]
  */
function ddw_cpi_add_plugin_installer_screen_options() {
 
	$string = sprintf(
		_x( '%s per page', 'Plugin Cards/ Plugins per page', 'cleaner-plugin-installer' ),
		( version_compare( $GLOBALS[ 'wp_version' ], '4.0', '>=' ) ) ? __( 'Plugin Cards', 'cleaner-plugin-installer' ) : __( 'Plugins', 'cleaner-plugin-installer' )
	);

	$option = 'per_page';
 
	$args = array(
		'label'   => $string,
		'default' => 24,
		'option'  => 'cpi_plugin_cards_per_page',
	);
 
	add_screen_option( $option, $args );

}  // end function

 
add_filter( 'set-screen-option', 'ddw_cpi_plugin_installer_set_screen_option', 10, 3 );
/**
 * Set screen option for plugin cards per page.
 *
 * See this link why only returning the $value here is correct:
 * @link   https://www.joedolson.com/2013/01/custom-wordpress-screen-options/
 *
 * @since  1.0.0
 *
 * @param  $status
 * @param  string $option
 * @param  int $value
 *
 * @return int $value Value for plugin cards per page.
 */
function ddw_cpi_plugin_installer_set_screen_option( $status, $option, $value ) {

	return $value;
 
}  // end function


/**
 * Validating user screen option for plugin cards per page option.
 *
 * @since  1.0.0
 *
 * @uses   get_current_user_id()
 * @uses   get_current_screen()
 * @uses   get_option()
 * @uses   get_user_meta()
 *
 * @global string $GLOBALS[ 'pagenow' ]
 *
 * @return absint $per_page Value for plugin cards per page.
 */
function ddw_cpi_plugin_installer_plugin_cards_per_page() {

	$user   = get_current_user_id();
	$screen = get_current_screen();

	/**
	 * Bail early if not on a plugin-install.php!
	 *    We're using check against $GLOBALS[ 'pagenow' ] here, as check against
	 *    $screen->id is not Multisite compatible in this specific case.
	 */
	if ( 'plugin-install.php' != $GLOBALS[ 'pagenow' ] ) {

		return;

	}  // end if

	/** Retrieve our "per page" sreen option */
	$screen_option = $screen->get_option( 'per_page', 'option' );

	/** Get option's setting for the current user */
	$per_page = get_user_meta( $user, $screen_option, TRUE );

	/** Use default if empty or < 1 */
	if ( empty( $per_page ) || $per_page < 1 ) {

		$per_page = $screen->get_option( 'per_page', 'default' );

	}  // end if

	/** Return the value (absolute integer) */
	return absint( $per_page );

}  // end function


add_filter( 'plugins_api_args', 'ddw_cpi_tweak_plugins_api_args', 10, 2 );
/**
 * Apply screen options for Plugins API arguments: plugin cards per page.
 *
 * @since  1.0.0
 *
 * @uses   ddw_cpi_plugin_installer_plugin_cards_per_page()
 *
 * @return object $args Tweaked arguments for Plugins API.
 */
function ddw_cpi_tweak_plugins_api_args( $args, $action ) {

	$args->per_page = ddw_cpi_plugin_installer_plugin_cards_per_page();

	return $args;

}  // end function


add_filter( 'install_plugins_table_api_args_search',    'ddw_cpi_tweak_plugins_api_tabs_args', 10, 1 );
add_filter( 'install_plugins_table_api_args_popular',   'ddw_cpi_tweak_plugins_api_tabs_args', 10, 1 );
add_filter( 'install_plugins_table_api_args_new',       'ddw_cpi_tweak_plugins_api_tabs_args', 10, 1 );
add_filter( 'install_plugins_table_api_args_favorites', 'ddw_cpi_tweak_plugins_api_tabs_args', 10, 1 );
/**
 * Apply screen options for Plugins API arguments - tab specific: plugin cards
 *    per page.
 *
 * @since  1.0.3
 *
 * @uses   ddw_cpi_plugin_installer_plugin_cards_per_page()
 *
 * @return array $args Tweaked arguments for Plugins API - tab specific.
 */
function ddw_cpi_tweak_plugins_api_tabs_args( $args ) {

	$args[ 'per_page' ] = ddw_cpi_plugin_installer_plugin_cards_per_page();

	return $args;

}  // end function