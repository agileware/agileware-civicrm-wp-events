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
  $postarr = agileware_civicrm_wp_events_make_postarray($objectRef->ID);
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
  $postarr = agileware_civicrm_wp_events_make_postarray($event_id);
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

//Function to find and insert the custom field tags
function agileware_civicrm_wp_events_insert_tag($event_tag_id) {
  if (empty($event_tag_id)) {
    return;
  }

  //get the custom field to later get the entities inside
  $tag_field = civicrm_api3('CustomField', 'getsingle', array(
  'id' => "14", //this ID will need to be found through civicrm API Explorer, 
                //execute 'CustomField -> get' and find the id of a result with a name/label that matches the
                //name you specified
  ));

  //now we're getting a final result based on the field provided
  //The field has the option_group_id specified in it, so we simply use that
  //Since this particular field is a multi-choice, the values we give need to be set in an array
  $tag_result = civicrm_api3('OptionValue', 'get', array(
  'option_group_id' => $tag_field["option_group_id"],
  'value' => array("IN" => $event_tag_id),
  ));

  // Insert the term if it does not already exist.
  //in this case, it is checking each defined tag in the results to see if any of them exist
  //within the given custom wordpress taxonomy
  $tag_name = array();
  foreach($tag_result["values"] as $ind_tag) {
    $tag_name[] = $ind_tag['label'];
    if (!term_exists($ind_tag['label'], 'aa-event-tag')) {
      wp_insert_term($ind_tag['label'], 'aa-event-tag');
    }
  }
    //when finished, return the final array with the value labels
    return $tag_name;
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

function agileware_civicrm_wp_events_make_postarray($event_id) {
  $event = civicrm_api3('Event', 'getsingle', array('id' => $event_id));

  if (empty($result['is_error'])) {
    $summary    = $event['summary'];
    $start_date = $event['start_date'];
    $end_date   = $event['end_date'];
    $is_active  = $event['is_active'];
    $is_public  = $event['is_public'];
    $type_id    = $event['event_type_id'];
    //setting the tag_id to be used for the final result
    //to find that, use civicrm API explorer
    //Determine the id vis 'CustomField -> get'
    //find the id of the entity with familiar values for 'name' or 'label'
    $tag_id    = $event['custom_14'];
    $title      = $event['title'];

    $type_name = agileware_civicrm_wp_events_insert_type($type_id);
    $tag_name = agileware_civicrm_wp_events_insert_tag($tag_id); //returns an array of tag value labels
    $city = agileware_civicrm_wp_events_insert_location($event_id);

    $postarr = array(
      'post_title'   => $title,
      'post_content' => '[civicrm component="event" id="' . $event_id . '" action="info" mode="live" hijack="1"]',
      'post_type' => 'aa-event',
      'post_status' => $is_active ? 'publish' : 'draft',
      'post_excerpt' => $summary,
      'tax_input' => array(
        'aa-event-type' => array($type_name),
        'aa-event-tag' => $tag_name, //already defined as an array
        'aa-event-location' => array($city),
      ),
      'meta_input' => array(
         'aa-event-id'      => $event_id,
         'aa-event-start'   => $start_date,
         'aa-event-end'     => $end_date,
         'aa-event-public'  => ($is_public ? 'on' : ''),
      ),
    );
    return $postarr;
  }
}
