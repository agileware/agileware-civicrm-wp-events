<?php /*
--------------------------------------------------------------------------------
Plugin Name: Agileware CiviCRM Events
Description: Create Wordpress events from CiviCRM events
Version: 0.0.1
Author: Vaibhav Sagar
Author URI: http://www.agileware.com.au
Plugin URI: https://bitbucket.com/agileware/agileware-civicrm-wp-events
--------------------------------------------------------------------------------
*/

// store reference to this file
if (!defined( 'AGILEWARE_CIVICRM_WP_EVENTS_FILE')) {
  define('AGILEWARE_CIVICRM_WP_EVENTS_FILE', __FILE__ );
}

// store PATH to this plugin's directory
if ( ! defined( 'AGILEWARE_CIVICRM_WP_EVENTS_PATH' ) ) {
	define( 'AGILEWARE_CIVICRM_WP_EVENTS_PATH', plugin_dir_path(AGILEWARE_CIVICRM_WP_EVENTS_FILE));
}

require(AGILEWARE_CIVICRM_WP_EVENTS_PATH . "agileware-civicrm-wp-events-metaboxes.php");
require(AGILEWARE_CIVICRM_WP_EVENTS_PATH . "agileware-civicrm-wp-events-civi.php");

// Create custom post type.
add_action('init',       'agileware_civicrm_wp_events_init');
add_action('admin_init', 'agileware_civicrm_wp_events_admin_init');
add_action('save_post',  'agileware_civicrm_wp_events_save_post');

add_action('civicrm_post', 'agileware_civicrm_wp_events_create', 10, 4);
add_action('civicrm_post', 'agileware_civicrm_wp_events_update', 10, 4);
add_action('civicrm_post', 'agileware_civicrm_wp_events_delete', 10, 4);

wp_enqueue_style('agileware_civicrm_wp_events_css', plugins_url('agileware-civicrm-wp-events.css', __FILE__));

function agileware_civicrm_wp_events_init() {
  agileware_civicrm_wp_events_create_post_type();
  agileware_civicrm_wp_events_register_taxonomies();
}

function agileware_civicrm_wp_events_create_post_type() {
  register_post_type('aa-event', array(
    'labels' => array(
      'name' => __('Events'),
      'singular_name' => __('Event'),
    ),
    'public' => true,
    'has_archive' => false,
    'show_ui' => true,
    'rewrite' => array('slug' => 'events'),
    'supports' => array(
      'title',
      'editor',
      'excerpt',
      'thumbnail',
      'author',
      'revisions'
    ),
  ));
}

function agileware_civicrm_wp_events_register_taxonomies() {
  register_taxonomy('aa-event-type', array('aa-event'), array(
    'labels' => array(
      'name' => __('Event Types'),
      'singular_name' => __('Event Type'),
    ),
    'show_ui' => true,
    'hierarchical' => false,
    'rewrite' => array('slug' => 'type'),
  ));
  register_taxonomy('aa-event-location', array('aa-event'), array(
    'labels' => array(
      'name' => __('Event Locations'),
      'singular_name' => __('Event Location'),
    ),
    'show_ui' => true,
    'hierarchical' => false,
    'rewrite' => array('slug' => 'location'),
  ));
}

// Custom Cornerstone element.
function agileware_civicrm_wp_events_custom_elements() {
  require_once 'agileware-civicrm-wp-events-cs-element-recent-events.php';
  cornerstone_add_element('AA_Upcoming_Events');
}
add_action( 'cornerstone_load_elements', 'agileware_civicrm_wp_events_custom_elements' );

// Define and register shortcodes.
require_once 'agileware-civicrm-wp-events-shortcodes.php';
