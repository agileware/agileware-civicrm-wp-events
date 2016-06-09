<?php

function agileware_civicrm_wp_events_admin_init() {
  agileware_civicrm_wp_events_add_meta_boxes();
}

function agileware_civicrm_wp_events_add_meta_boxes(){
  add_meta_box('aa-event-civicrm', 'CiviCRM Event', '_agileware_civicrm_wp_aa_event_civicrm', 'aa-event', 'side');

  if ( function_exists('acf_add_local_field_group') ):

    acf_add_local_field_group(array (
      'key' => 'group_aa_event_details',
      'title' => 'Event details',
      'fields' => array (
        array (
          'key' => 'aa-event-start',
          'label' => 'Event start',
          'name' => 'aa-event-start',
          'type' => 'date_time_picker',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'show_date' => 'true',
          'date_format' => 'yy-mm-dd',
          'time_format' => 'hh:mm:ss',
          'show_week_number' => 'false',
          'picker' => 'select',
          'save_as_timestamp' => 'false',
          'get_as_timestamp' => 'false',
        ),
        array (
          'key' => 'aa-event-end',
          'label' => 'Event end',
          'name' => 'aa-event-end',
          'type' => 'date_time_picker',
          'instructions' => '',
          'required' => 0,
          'conditional_logic' => 0,
          'wrapper' => array (
            'width' => '',
            'class' => '',
            'id' => '',
          ),
          'show_date' => 'true',
          'date_format' => 'yy-mm-dd',
          'time_format' => 'hh:mm:ss',
          'show_week_number' => 'false',
          'picker' => 'select',
          'save_as_timestamp' => 'false',
          'get_as_timestamp' => 'false',
        ),
      ),
      'location' => array (
        array (
          array (
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'aa-event',
          ),
        ),
      ),
      'menu_order' => 0,
      'position' => 'normal',
      'style' => 'default',
      'label_placement' => 'top',
      'instruction_placement' => 'label',
      'hide_on_screen' => '',
      'active' => 1,
      'description' => '',
    ));

  endif;

}

function agileware_civicrm_wp_events_save_post(){
  global $post;
  $post_id = $post->ID;

  update_post_meta($post_id, 'aa-event-public',  $_POST['aa-event-public']);
  update_post_meta($post_id, 'aa-event-id',      $_POST['aa-event-id']);
}

function _agileware_civicrm_wp_aa_event_civicrm() {
  global $post;
  $custom = get_post_custom($post->ID);
  $mb_id = $custom['aa-event-id'][0];
  $mb_public = $custom['aa-event-public'][0];
  ?>
    <div class="wrap">
      <p>
        <label class="screen-reader-text" for="aa-event-public">ID</label>
        <input name="aa-event-id" size="4" value="<?php echo $mb_id; ?>" readonly />
        <label for="aa-event-id">ID</label>
      </p>
      <label class="screen-reader-text" for="aa-event-public">Public</label>
      <input type="checkbox" name="aa-event-public" id="aa-event-public" <?php if ($mb_public=='on'){echo 'checked';}?>></input>
      <label for="aa-event-public">Public</label>
      <p id="aa-event-civicrm-help">CiviCRM event specific fields.</p>
    </div>
  <?php
}
