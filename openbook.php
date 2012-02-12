<?php
/*
Plugin Name: OpenBook
Plugin URI: http://wordpress.org/extend/plugins/openbook-book-data/
Description: Displays a book's cover image, title, author, links, and other book data from Open Library.
Version: 3.3.0
Author: John Miedema
Author URI: http://code.google.com/p/openbook4wordpress/

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

include_once('openbook_main.php');

if ( ! defined( 'ABSPATH' ) )
	die( "Can't load this file directly" );

class MyOpenBook
{
	function __construct() {
		register_activation_hook(__FILE__, 'ob_activation_check');
		register_deactivation_hook(__FILE__, 'ob_deactivation');
		add_action( 'admin_init', array( $this, 'action_admin_init' ) );
		add_action('admin_menu', 'openbook_add_pages');
		add_shortcode('openbook', 'openbook_insertbookdata');
		add_filter('widget_text', 'do_shortcode'); //allows shortcodes in widgets
		add_action('wp_enqueue_scripts', 'openbook_add_stylesheet'); //add stylesheet
	}

	function action_admin_init() {
		// only hook up these filters if we're in the admin panel, and the current user has permission
		// to edit posts and pages
		if ( current_user_can( 'edit_posts' ) ) {
			add_filter( 'mce_buttons', array( $this, 'filter_mce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'filter_mce_plugin' ) );
			add_filter('mce_css', 'filter_mce_css');
			$plugin = plugin_basename(__FILE__);
			add_filter( 'plugin_action_links_' . $plugin, array( $this, 'filter_plugin_actions_links'), 10, 2);
			openbook_add_stylesheet();
		}
	}

	function filter_mce_button( $buttons ) {
		// add a separation before our button
		array_push( $buttons, '|', 'openbook_button' );
		return $buttons;
	}

	function filter_mce_plugin( $plugins ) {
		// this plugin file will work the magic of our button
		$plugins['openbook'] = plugin_dir_url( __FILE__ ) . 'libraries/openbook_button.js';
		return $plugins;
	}

	function filter_plugin_actions_links($links, $file)
	{
		$settings_link = $settings_link = '<a href="options-general.php?page=openbook_options.php">' . __('Settings') . '</a>';
		array_unshift($links, $settings_link);
		return $links;
	}
}

//handles any processing when the plugin is activated
function ob_activation_check() {

	$plugin = trim( $GET['plugin'] );

	//if json_decode is missing (< PHP5.2) use local json library
	if(!function_exists('json_decode')) {
		include_once('libraries/openbook_json.php');
		function json_decode($data) {
			$json = new Services_JSON_ob();
			return( $json->decode($data) );
		}
	}

	//test if cURL is enabled
	if (!function_exists('curl_init')) {
		deactivate_plugins($plugin);
		wp_die(OB_ENABLECURL_LANG);
	}

	//initialize options
	openbook_utilities_setDefaultOptions();
}

//handles any cleanup when plugin is deactivated
function ob_deactivation() {
	$savetemplates = get_option(OB_OPTION_SAVETEMPLATES_NAME);
	if ($savetemplates!=OB_HTML_CHECKED_TRUE) {
		openbook_utilities_deleteOptions();
	}
}

// action function for admin hooks
function openbook_add_pages() {
	add_options_page('OpenBook', 'OpenBook', 8, 'openbook_options.php', 'openbook_options_page'); // add a new submenu under Options:
}

// displays the page content for the options submenu
function openbook_options_page() {
	require_once('openbook_options.php');
}

$myopenbook = new MyOpenBook();

add_action('wp_ajax_openbook_action', 'openbook_action_callback');

//server-side call for ajax visual editor button
function openbook_action_callback() {

	$booknumber = $_POST['booknumber'];
	$templatenumber = $_POST['templatenumber'];
	$publisherurl = $_POST['publisherurl'];
	$revisionnumber = $_POST['revisionnumber'];

	$shortcode_array = array( 'booknumber' => $booknumber, 'templatenumber' => $templatenumber, 'publisherurl' => $publisherurl, 'revisionnumber' => $revisionnumber);

	$ret = openbook_insertbookdata($shortcode_array, null);
	echo $ret;
	die();
}

//add custom stylesheet
function openbook_add_stylesheet() {
	$myStyleUrl = plugins_url('libraries/openbook_style.css', __FILE__); // Respects SSL, Style.css is relative to the current file
    $myStyleFile = WP_PLUGIN_DIR . '/openbook-book-data/libraries/openbook_style.css';
    if ( file_exists($myStyleFile) ) {
    	wp_register_style('openbook', $myStyleUrl);
        wp_enqueue_style( 'openbook');
    }
}

//returns stylesheet for visual editor
function filter_mce_css($url) {
	if(!empty($url)) $url .= ',';
	$url .= plugin_dir_url( __FILE__ ) . 'libraries/openbook_style.css';
	return $url;
}

?>