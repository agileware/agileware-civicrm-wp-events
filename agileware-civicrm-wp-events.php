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

// Create custom post type.
add_action('init',       'agileware_civicrm_wp_events_init');
add_action('admin_init', 'agileware_civicrm_wp_events_admin_init');
add_action('save_post',  'agileware_civicrm_wp_events_save_post');

// Hook into CiviCRM.
add_action('civicrm_post', 'agileware_civicrm_wp_events_create', 10, 4);
add_action('civicrm_post', 'agileware_civicrm_wp_events_update', 10, 4);
add_action('civicrm_post', 'agileware_civicrm_wp_events_delete', 10, 4);

function agileware_civicrm_wp_events_init() {
  agileware_civicrm_wp_events_create_post_type();
  agileware_civicrm_wp_events_register_taxonomies();
}

function agileware_civicrm_wp_events_admin_init() {
  agileware_civicrm_wp_events_add_meta_boxes();
}

function agileware_civicrm_wp_events_create_post_type() {
  register_post_type('event', array(
    'labels' => array(
      'name' => __('Events'),
      'singular_name' => __('Event'),
    ),
    'public' => true,
    'has_archive' => false,
    'rewrite' => array('slug' => 'events'),
  ));
}

function agileware_civicrm_wp_events_register_taxonomies() {
  register_taxonomy('events_type', array('event'), array(
    'labels' => array(
      'name' => __('Event Types'),
      'singular_name' => __('Event Type'),
    ),
    'show_ui' => true,
    'hierarchical' => false,
    'rewrite' => array('slug' => 'type'),
  ));
  register_taxonomy('events_location', array('event'), array(
    'labels' => array(
      'name' => __('Event Locations'),
      'singular_name' => __('Event Location'),
    ),
    'show_ui' => true,
    'hierarchical' => false,
    'rewrite' => array('slug' => 'location'),
  ));
}

function agileware_civicrm_wp_events_add_meta_boxes(){
  add_meta_box('events_mb_start',   'Event Start',   '_agileware_civicrm_wp_events_mb_start',   'event', 'normal');
  add_meta_box('events_mb_end',     'Event End',     '_agileware_civicrm_wp_events_mb_end',     'event', 'normal');
  add_meta_box('events_mb_summary', 'Event Summary', '_agileware_civicrm_wp_events_mb_summary', 'event', 'normal');
  add_meta_box('events_mb_public',  'Public Event',  '_agileware_civicrm_wp_events_mb_public',  'event', 'side');
  add_meta_box('events_mb_id',      'Event ID',      '_agileware_civicrm_wp_events_mb_id',      'event', 'side');
}

function _agileware_civicrm_wp_events_mb_id() {
  global $post;
  $custom = get_post_custom($post->ID);
  $mb_id = $custom['events_mb_id'][0];
  ?>
    <div class="wrap">
      <p>
        <label>CiviCRM Event ID:</label>
        <input name="events_mb_id" value="<?php echo $mb_id; ?>" />
      </p>
    </div>
  <?php
}

function _agileware_civicrm_wp_events_mb_start() {
  global $post;
  $custom = get_post_custom($post->ID);
  $mb_start = $custom['events_mb_start'][0];
  ?>
    <div class="wrap">
      <p>
        <input name="events_mb_start" value="<?php echo $mb_start; ?>" />
      </p>
    </div>
  <?php
}

function _agileware_civicrm_wp_events_mb_end() {
  global $post;
  $custom = get_post_custom($post->ID);
  $mb_end = $custom['events_mb_end'][0];
  ?>
    <div class="wrap">
      <p>
        <input name="events_mb_end" value="<?php echo $mb_end; ?>" />
      </p>
    </div>
  <?php
}

function _agileware_civicrm_wp_events_mb_summary() {
  global $post;
  $custom = get_post_custom($post->ID);
  $mb_summary = $custom['events_mb_summary'][0];
  ?>
    <div class="wrap">
      <p>
        <textarea name="events_mb_end"><?php echo $mb_summary; ?></textarea>
      </p>
    </div>
  <?php
}

function _agileware_civicrm_wp_events_mb_public() {
  global $post;
  $custom = get_post_custom($post->ID);
  $mb_public = $custom['events_mb_public'][0];
  ?>
    <div class="wrap">
      <p>
        <input type="checkbox" name="events_mb_public" <?php if ($mb_public=='on'){echo 'checked';}?>></input>
      </p>
    </div>
  <?php
}

function agileware_civicrm_wp_events_save_post(){
  global $post;
  $post_id = $post->ID;
  update_post_meta($post_id, 'events_mb_id',      $_POST['events_mb_id']);
  update_post_meta($post_id, 'events_mb_start',   $_POST['events_mb_start']);
  update_post_meta($post_id, 'events_mb_end',     $_POST['events_mb_end']);
  update_post_meta($post_id, 'events_mb_summary', $_POST['events_mb_summary']);
  update_post_meta($post_id, 'events_mb_public',  $_POST['events_mb_public']);
}

function agileware_civicrm_wp_events_create($op, $objectName, $objectId, $objectRef) {
  if ($op != 'create') {
    return;
  }
  if ($objectName != 'Event') {
    return;
  }
  civicrm_initialize();
  if (!$objectRef instanceof CRM_Event_DAO_Event) {
    return;
  }
  $postarr = agileware_civicrm_wp_events_make_postarray($objectRef); 
  return wp_insert_post($postarr);
}

function agileware_civicrm_wp_events_update($op, $objectName, $objectId, $objectRef) {
  if ($op != 'edit') {
    return;
  }
  if ($objectName != 'Event') {
    return;
  }
  civicrm_initialize();
  if (!$objectRef instanceof CRM_Event_DAO_Event) {
    return;
  }
  $event_id = $objectRef->id;
  $event_posts = get_posts(array(
    'post_type'   => 'event',
    'post_status' => 'publish,draft',
    'meta_key'    => 'events_mb_id',
    'meta_value'  => $event_id,
  ));
  error_log(print_r($event_posts, true));
  if (empty($event_posts)) {
    // The corresponding event post has not been created.
    $post_id = 0;
  }
  else {
    $my_post = $event_posts[0];
    $post_id = $my_post->ID; 
  }
  $postarr = agileware_civicrm_wp_events_make_postarray($objectRef); 
  $postarr['ID'] = $post_id;
  return wp_insert_post($postarr);
}

function agileware_civicrm_wp_events_delete($op, $objectName, $objectId, $objectRef) {
  if ($op != 'delete') {
    return;
  }
  if ($objectName != 'Event') {
    return;
  }
  civicrm_initialize();
  error_log(print_r(array(
  'op' => $op,
  'objectName' => $objectName,
  'objectId' => $objectId,
  //'objectRef' => $objectRef  
  ), true));
}

function agileware_civicrm_wp_events_insert_type($event_type_id) {
  $type_result = civicrm_api3('OptionValue', 'getsingle', array(
    'option_group_id' => 'event_type',
    'value' => $event_type_id,
  ));
  // Insert the term if it does not already exist.
  if (empty($type_result['is_error'])) {
    $type_name = $type_result['name'];
    if (!term_exists($type_name, 'events_type')) {
      wp_insert_term($type_name, 'events_type');
    }
    return $type_name;
  }
}

function agileware_civicrm_wp_events_insert_location($event_id) {
  // Get the City from the Address from the Location Block for the Event.
  $chained_loc_result = civicrm_api3('Event', 'getsingle', array(
    'id' => $event_id,
    'api.LocBlock.getsingle' => array(),
  ));
  if (empty($chained_loc_result['is_error'])) {
    $address_id = $chained_loc_result['api.LocBlock.getsingle']['address_id'];
    $address_result = civicrm_api3('Address', 'getsingle', array(
      'id' => $address_id,
    )); 
    if (empty($address_result['is_error'])) {
      $city = $address_result['city'];
      if (!term_exists($city, 'events_location')) {
        wp_insert_term($city, 'events_location');
      }
      return $city;
    }
  }
}

function agileware_civicrm_wp_events_make_postarray($event) {
  $event_id   = $event->id;
  $summary    = $event->summary;
  $start_date = $event->start_date;
  $end_date   = $event->start_date;
  $is_active  = $event->is_active;
  $is_public  = $event->is_public;
  $type_id    = $event->event_type_id;
  $title      = $event->title;
  
  $type_name = agileware_civicrm_wp_events_insert_type($type_id);
  $city = agileware_civicrm_wp_events_insert_location($event_id);
  
  $postarr = array(
    'post_title'   => $title,
    'post_content' => '[civicrm component="event" id="' . $event_id . '" action="info" mode="live" hijack="1"]',
    'post_type' => 'event',
    'post_status' => $is_active ? 'publish' : 'draft',
    'tax_input' => array(
      'events_type' => array($type_name),
      'events_location' => array($city),
    ),
    'meta_input' => array(
       'events_mb_id'      => $event_id,     
       'events_mb_start'   => $start_date,  
       'events_mb_end'     => $end_date,    
       'events_mb_summary' => $summary,
       'events_mb_public'  => ($is_public ? 'on' : ''), 
    ),
  );
  return $postarr;
}
