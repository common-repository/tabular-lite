<?php
/*
Plugin Name: Tabular Lite
Plugin URI: http://wordpress.org/plugins/tabular-lite/
Description: Add responsive tables and graphs in WordPress using the advanced D3.js visualization library.
Version: 0.1.0
Author: David Gwyer
Author URI: http://www.wpgoplugins.com
*/

/*  Copyright 2017 David Gwyer (email : david@wpgoplugins.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Bootstrap class for Tabular. */
class WPGO_Tabular {

	protected $module_roots;

	/* Main class constructor. */
	public function __construct($module_roots) {

		$this->module_roots = $module_roots;

		add_action( 'plugins_loaded', array( &$this, 'load_supported_features' ) );
		add_action( 'plugins_loaded', array( &$this, 'localize_plugin' ) );
		add_filter( 'plugin_action_links', array( &$this, 'plugin_get_started_link'), 10, 2 );
	}

	/* Check for specific CPT used in the current WPGO theme. */
	public function load_supported_features() {

		$root = $this->module_roots['dir'];

		// Tabular CPT
		require_once( $root . 'classes/tabular-cpt.php' );
		new WPGO_Tabular_CPT($this->module_roots);

		// Tabular enqueue functions
		require_once( $root . 'classes/tabular-enqueue-scripts.php' );
		new WPGO_Tabular_Enqueue_Scripts($this->module_roots);

		// Tabular data CPT
		//require_once( $root . 'classes/tabular-data-cpt.php' );
		//new WPGO_Tabular_Data_CPT($this->module_roots);

		// Tabular about page
		require_once( $root . 'classes/tabular-about.php' );
		new WPGO_Tabular_About_Page($this->module_roots);

		// Tabular Shortcodes
		require_once( $root . 'classes/tabular-shortcodes.php' );
		new WPGO_Tabular_Shortcodes($this->module_roots);

		// Tabular Settings
		//require_once( $root . 'classes/tabular-settings.php' );
		//new WPGO_Tabular_Options();

		// Tabular Settings
		require_once( $root . 'classes/tabular-builder.php' );

		// Tabular Templates
		require_once( $root . 'classes/tabular-templates.php' );

		// Allow shortcodes to be used in widgets. These callbacks are WordPress functions.
		add_filter( 'widget_text', 'shortcode_unautop' );
		add_filter( 'widget_text', 'do_shortcode' );
	}

	/**
	 * Add Plugin localization support.
	 */
	public function localize_plugin() {
		load_plugin_textdomain( 'tabular', false, plugin_basename( $this->module_roots['dir'] ) . '/languages' );
	}

	/* Display a 'Get Started' link on the main Plugins page. */
	public function plugin_get_started_link( $links, $file ) {

		if ( $file == plugin_basename( __FILE__ ) ) {
			$new_links = '<a href="' . get_admin_url() . 'edit.php?post_type=wpgo_tabular&page=wpgo-tabular-about-page">' . __( 'Get Started' ) . '</a>';
			/* Make the 'Settings' link appear first. */
			array_unshift( $links, $new_links );
		}

		/*if ( $file == plugin_basename( __FILE__ ) ) {
			$pccf_links = '<a style="color:#60a559;" href="https://tabularwp.com/" target="_blank" title="Go PRO - 100% money back guarantee"><span style="width:18px;height:18px;font-size:18px;" class="dashicons dashicons-flag"></span></a>';
			array_push( $links, $pccf_links );
		}*/

		return $links;
	}
} /* End class definition */

$module_roots = array(
	'dir' => plugin_dir_path( __FILE__ ),
	'uri' => plugins_url( '', __FILE__ ),
	'__FILE__' => __FILE__
);
new WPGO_Tabular( $module_roots );
