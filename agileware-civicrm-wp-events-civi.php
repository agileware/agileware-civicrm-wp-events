<?php

// Hook into CiviCRM.

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
    'post_type'   => 'aa-event',
    'post_status' => 'publish,draft',
    'meta_key'    => 'aa-event-id',
    'meta_value'  => $event_id,
  ));
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
  if (!$objectRef instanceof CRM_Event_DAO_Event) {
    return;
  }
  $event_id = $objectRef->id;
  $event_posts = get_posts(array(
    'post_type'   => 'aa-event',
    'post_status' => 'publish,draft',
    'meta_key'    => 'aa-event-id',
    'meta_value'  => $event_id,
  ));
  if (!empty($event_posts)) {
    // The corresponding event post has not been created.
    $my_post = $event_posts[0];
    $post_id = $my_post->ID;
    wp_delete_post($post_id, true);
  }
}

function agileware_civicrm_wp_events_insert_type($event_type_id) {
  if (empty($event_type_id)) {
    return;
  }
  $type_result = civicrm_api3('OptionValue', 'getsingle', array(
    'option_group_id' => 'event_type',
    'value' => $event_type_id,
  ));
  // Insert the term if it does not already exist.
  if (empty($type_result['is_error'])) {
    $type_name = $type_result['name'];
    if (!term_exists($type_name, 'aa-event-type')) {
      wp_insert_term($type_name, 'aa-event-type');
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
  if (empty($chained_loc_result['api.LocBlock.getsingle']['is_error'])) {
    $address_id = $chained_loc_result['api.LocBlock.getsingle']['address_id'];
    $address_result = civicrm_api3('Address', 'getsingle', array(
      'id' => $address_id,
    ));
    if (empty($address_result['is_error'])) {
      $city = $address_result['city'];
      if (!term_exists($city, 'aa-event-location')) {
        wp_insert_term($city, 'aa-event-location');
      }
      return $city;
    }
  }
}

function agileware_civicrm_wp_events_make_postarray($event) {
  $event_id   = $event->id;
  $summary    = $event->summary;
  $start_date = $event->start_date;
  $end_date   = $event->end_date;
  $is_active  = $event->is_active;
  $is_public  = $event->is_public;
  $type_id    = $event->event_type_id;
  $title      = $event->title;

  $type_name = agileware_civicrm_wp_events_insert_type($type_id);
  $city = agileware_civicrm_wp_events_insert_location($event_id);

  $postarr = array(
    'post_title'   => $title,
    'post_content' => '[civicrm component="event" id="' . $event_id . '" action="info" mode="live" hijack="1"]',
    'post_type' => 'aa-event',
    'post_status' => $is_active ? 'publish' : 'draft',
    'post_excerpt' => $summary,
    'tax_input' => array(
      'aa-event-type' => array($type_name),
      'aa-event-location' => array($city),
    ),
    'meta_input' => array(
       'aa-event-id'      => $event_id,
       'aa-event-start'   => $start_date,
       'aa-event-end'     => $end_date,
       'aa-event-public'  => ($is_public ? 'on' : ''),
    ),
  );
  error_log(print_r($postarr, true));
  return $postarr;
}
