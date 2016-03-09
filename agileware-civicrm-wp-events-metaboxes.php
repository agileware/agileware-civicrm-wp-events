<?php

function agileware_civicrm_wp_events_admin_init() {
  agileware_civicrm_wp_events_add_meta_boxes();
}

function agileware_civicrm_wp_events_add_meta_boxes(){
  add_meta_box('aa-event-start',   'Event Start',   '_agileware_civicrm_wp_aa_event_start',   'aa-event', 'normal');
  add_meta_box('aa-event-end',     'Event End',     '_agileware_civicrm_wp_aa_event_end',     'aa-event', 'normal');
  add_meta_box('aa-event-summary', 'Event Summary', '_agileware_civicrm_wp_aa_event_summary', 'aa-event', 'normal');
  add_meta_box('aa-event-public',  'Public Event',  '_agileware_civicrm_wp_aa_event_public',  'aa-event', 'side');
  add_meta_box('aa-event-id',      'Event ID',      '_agileware_civicrm_wp_aa_event_id',      'aa-event', 'side');
}

function agileware_civicrm_wp_events_save_post(){
  global $post;
  $post_id = $post->ID;
  update_post_meta($post_id, 'aa-event-id',      $_POST['aa-event-id']);
  update_post_meta($post_id, 'aa-event-start',   $_POST['aa-event-start']);
  update_post_meta($post_id, 'aa-event-end',     $_POST['aa-event-end']);
  update_post_meta($post_id, 'aa-event-summary', $_POST['aa-event-summary']);
  update_post_meta($post_id, 'aa-event-public',  $_POST['aa-event-public']);
}

function _agileware_civicrm_wp_aa_event_id() {
  global $post;
  $custom = get_post_custom($post->ID);
  $mb_id = $custom['aa-event-id'][0];
  ?>
    <div class="wrap">
      <p>
        <label>CiviCRM Event ID:</label>
        <input name="aa-event-id" value="<?php echo $mb_id; ?>" />
      </p>
    </div>
  <?php
}

function _agileware_civicrm_wp_aa_event_start() {
  global $post;
  $custom = get_post_custom($post->ID);
  $mb_start = $custom['aa-event-start'][0];
  ?>
    <div class="wrap">
      <p>
        <input name="aa-event-start" value="<?php echo $mb_start; ?>" />
      </p>
    </div>
  <?php
}

function _agileware_civicrm_wp_aa_event_end() {
  global $post;
  $custom = get_post_custom($post->ID);
  $mb_end = $custom['aa-event-end'][0];
  ?>
    <div class="wrap">
      <p>
        <input name="aa-event-end" value="<?php echo $mb_end; ?>" />
      </p>
    </div>
  <?php
}

function _agileware_civicrm_wp_aa_event_summary() {
  global $post;
  $custom = get_post_custom($post->ID);
  $mb_summary = $custom['aa-event-summary'][0];
  ?>
    <div class="wrap">
      <p>
        <textarea name="aa-event-end"><?php echo $mb_summary; ?></textarea>
      </p>
    </div>
  <?php
}

function _agileware_civicrm_wp_aa_event_public() {
  global $post;
  $custom = get_post_custom($post->ID);
  $mb_public = $custom['aa-event-public'][0];
  ?>
    <div class="wrap">
      <p>
        <input type="checkbox" name="aa-event-public" <?php if ($mb_public=='on'){echo 'checked';}?>></input>
      </p>
    </div>
  <?php
}
