<?php /*
--------------------------------------------------------------------------------
Plugin Name: CiviCRM WordPress Events
Description: Create Wordpress events from CiviCRM events
Version: 1.0.2
Author: Agileware
Author URI: http://www.agileware.com.au
Plugin URI: https://github.com/agileware/agileware-civicrm-wp-events
GitHub Plugin URI: https://github.com/agileware/agileware-civicrm-wp-events
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



function agileware_civicrm_wp_events_init() {
  wp_enqueue_style('agileware_civicrm_wp_events_css', plugins_url('agileware-civicrm-wp-events.css', __FILE__));
  agileware_civicrm_wp_events_create_post_type();
  agileware_civicrm_wp_events_register_taxonomies();
  flush_rewrite_rules(FALSE);
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

/**
 * Add Event specific sidebars.
 */
function agileware_civicrm_wp_events_sidebars() {
    register_sidebar( array(
        'name' => __( 'Events sidebar'),
        'id' => 'aa-events-sidebar',
        'description' => __( 'Widgets in this area will be shown on Events.'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="h-widget">',
        'after_title'   => '</h4>',
    ) );
    register_sidebar( array(
        'name' => __( 'Events (single) sidebar'),
        'id' => 'aa-events-single-sidebar',
        'description' => __( 'Widgets in this area will be shown on a single Event.'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4 class="h-widget">',
        'after_title'   => '</h4>',
    ) );
}
add_action( 'widgets_init', 'agileware_civicrm_wp_events_sidebars' );


require_once(AGILEWARE_CIVICRM_WP_EVENTS_PATH . 'inc/widgets/class-aa-events-upcoming-widget.php');
function agileware_civicrm_wp_events_register_widgets() {
  register_widget('AA_Events_Upcoming_Widget');
}
add_action('widgets_init', 'agileware_civicrm_wp_events_register_widgets');


/**
 * Initial setup and constants.
 */

/**
 * Enable additional supported features for specific post types.
 */
function agileapp_init() {
  // Enable additional supported features for specific post types.
  add_post_type_support( 'page', 'excerpt' );
}
// Fires after WordPress has finished loading but before any headers are sent.
add_action( 'init', 'agileapp_init' );

/**
 * Implementation of facetwp_is_main_query.
 * @see https://facetwp.com/documentation/facetwp_is_main_query/
 */
function agileapp_facetwp_is_main_query( $is_main_query, $query ) {
    if ( isset( $query->query_vars['facetwp'] ) ) {
        $is_main_query = true;
    }
    return $is_main_query;
}
add_filter( 'facetwp_is_main_query', 'agileapp_facetwp_is_main_query', 10, 2 );

// Enable shortcodes for the Text widget.
add_filter('widget_text', 'do_shortcode');



/**
 * FacetWP overrides.
 */
function agileapp_facetwp_index_row( $params, $class ) {
    switch ($params['facet_name']) {
      case 'event_date':
        // Save date in "MM, YYYY" format.
        $raw_value = $params['facet_value'];
        $params['facet_display_value'] = date('F, Y', strtotime($raw_value));
        $params['facet_value'] = $params['facet_display_value'];
        // d($params['facet_value']);
        break;
    }
    return $params;
}
add_filter('facetwp_index_row', 'agileapp_facetwp_index_row', 10, 2);
