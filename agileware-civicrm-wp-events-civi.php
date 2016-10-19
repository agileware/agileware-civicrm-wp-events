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
  $postarr = agileware_civicrm_wp_events_make_postarray($objectRef->id);
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
    'post_status' => 'publish,draft,private',
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
    'post_status' => 'publish,draft,private',
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

//hooks for custom fields
//op - operation performed (should be 'create')
//groupID - the ID of the custom field group
//entityID - the ID of the object the custom field is in (in this case, it should be the event ID)
//params - an array of separate parameters brought with the custom field.
function agileware_civicrm_wp_events_custom_field($op, $groupID, $entityID, $params )
{
  //make sure this is a create event
  if (($op != 'create')&&($op != 'edit')) {
    return;
  }

  //get the parameter array inside the params array, since the argument is a nested array
  $paramTable = $params[0];

  //make sure that this is done for creating a civi event
  if ($paramTable['entity_table'] != 'civicrm_event') {
    return;
  }

  civicrm_initialize();

  $event_id = $entityID;
  $event_posts = get_posts(array(
    'post_type'   => 'aa-event',
    'post_status' => 'publish,draft,private',
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
add_action('civicrm_custom', 'agileware_civicrm_wp_events_custom_field', 10, 4);


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

function agileware_civicrm_wp_events_get_custom_id($custom_label) {
  $custom_set = civicrm_api3('CustomField', 'getsingle', array(
    'label' => $custom_label,
  ));

  $custom_id_get = $custom_set["id"];
  return $custom_id_get;
}

//Function to find and insert the custom field tags
function agileware_civicrm_wp_events_insert_tag($event_tag_id, $tag_field_id) {
  if (empty($event_tag_id)) {
    return;
  }

  //get the custom field to later get the entities inside
  $tag_field = civicrm_api3('CustomField', 'getsingle', array(
  'id' => $tag_field_id, //this ID will need to be found through civicrm API Explorer,
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

//Function to find and insert the custom field hosts
function agileware_civicrm_wp_events_insert_host($event_host_id, $host_field_id) {
  if (empty($event_host_id)) {
    return;
  }

  //get the custom field to later get the entities inside
  $host_field = civicrm_api3('CustomField', 'getsingle', array(
  'id' => $host_field_id, //this ID will need to be found through civicrm API Explorer,
                //execute 'CustomField -> get' and find the id of a result with a name/label that matches the
                //name you specified
  ));

  //now we're getting a final result based on the field provided
  //The field has the option_group_id specified in it, so we simply use that
  //Since this particular field is a multi-choice, the values we give need to be set in an array
  //since this is the host, there is only one possible value
  $host_result = civicrm_api3('OptionValue', 'get', array(
  'option_group_id' => $host_field["option_group_id"],
  'value' => $event_host_id,
  ));

  // Insert the term if it does not already exist.
  //in this case, it is checking each defined host in the results to see if any of them exist
  //within the given custom wordpress taxonomy
  $host_name = array();
  foreach($host_result["values"] as $ind_host) {
    $host_name[] = $ind_host['label'];
    if (!term_exists($ind_host['label'], 'aa-event-host')) {
      wp_insert_term($ind_host['label'], 'aa-event-host');
    }
  }
    //when finished, return the final array with the value labels
    return $host_name;
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

    //get the ID of the tag custom field
    $tag_field_id = agileware_civicrm_wp_events_get_custom_id("event tags");
    //get the ID of the host custom field
    $host_field_id = agileware_civicrm_wp_events_get_custom_id("event hosts");

    $summary    = $event['summary'];
    $start_date = $event['start_date'];
    $end_date   = $event['end_date'];
    $is_active  = $event['is_active'];
    $is_public  = $event['is_public'];
    $type_id    = $event['event_type_id'];


//Kint::dump($start_date);
//Kint::dump($end_date);
//Kint::trace();

    //setting the tag_id to be used for the final result
    //find the id of the entity with familiar values for 'name' or 'label'
    $tag_id_event_label = 'custom_' . $tag_field_id;
    $tag_id    = $event[$tag_id_event_label];
    $host_id_event_label = 'custom_' . $host_field_id;
    $host_id    = $event[$host_id_event_label];
    $title      = $event['title'];

    $type_name = agileware_civicrm_wp_events_insert_type($type_id);
    $tag_name = agileware_civicrm_wp_events_insert_tag($tag_id, $tag_field_id); //returns an array of tag value labels
    $host_name = agileware_civicrm_wp_events_insert_host($host_id, $host_field_id); //returns an array of host value labels
    $city = agileware_civicrm_wp_events_insert_location($event_id);

    //prepare the tags for display on the main page
    $tag_name_content = '';
    $tag_count = count($tag_name);
    $tag_current_index=0;

    if ($tag_count == 0)
    {
      $tag_name_content .= '<span class="event-tag no-tags">None</span>';
    }
    else {
      foreach ($tag_name as $new_tag) {

        //get the tag in wordpress by its name
        $new_wp_tag=get_term_by('name', $new_tag, 'aa-event-tag');

        //ensure the tag exists (the result will be false if no tag was found)
        if ($new_wp_tag)
        {
          $wp_tag_slug = $new_wp_tag->slug;

          $tag_name_content .= '<a href="/events/?fwp_event_tag=' . $wp_tag_slug
                            . '" title="'
                            . esc_attr( sprintf( __( "View all events tagged by: &ldquo;%s&rdquo;", '__x__' ), $new_tag ) )
                            . '">';
          $tag_name_content .= '<span class="event-tag">';
          //$tag_name_content .= '<a href="#">' . $new_tag . '</a> ';
          $tag_name_content .= $new_tag;
          //add a comma if the current tag isn't the last one in the loop.
          if ($tag_current_index != $tag_count - 1)
            { $tag_name_content .= ', '; }
          else
            { $tag_name_content .= ' '; }
          $tag_name_content .= '</span>';
          $tag_name_content .= '</a>';

          $tag_current_index+=1;
        }
      }
    }

    //format the content so that the tags will display at the top of the page.
    $final_post_content = '<h2 class="h6">Tags: </h2>' . $tag_name_content
      . '[civicrm component="event" id="' . $event_id . '" action="info" mode="live" hijack="0"]';

      //determine the post status, whether the event is active and set as a public event
      $final_post_status = 'publish';
      if (!$is_active) {
        $final_post_status = 'draft';
      }
      else if (!$is_public) {
        $final_post_status = 'private';
      }

    $postarr = array(
      'post_title'   => $title,
      'post_content' => $final_post_content,
      'post_type' => 'aa-event',
      'post_status' => $final_post_status,
      'post_excerpt' => $summary,
      'tax_input' => array(
        'aa-event-type' => array($type_name),
        'aa-event-tag' => $tag_name, //already defined as an array
        'aa-event-host' => $host_name, //already defined as an array
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
