<?php

function agileware_civicrm_wp_events_admin_init() {
  agileware_civicrm_wp_events_add_meta_boxes();
}

function agileware_civicrm_wp_events_add_meta_boxes(){
  add_meta_box('events_mb_start',   'Event Start',   '_agileware_civicrm_wp_events_mb_start',   'event', 'normal');
  add_meta_box('events_mb_end',     'Event End',     '_agileware_civicrm_wp_events_mb_end',     'event', 'normal');
  add_meta_box('events_mb_summary', 'Event Summary', '_agileware_civicrm_wp_events_mb_summary', 'event', 'normal');
  add_meta_box('events_mb_public',  'Public Event',  '_agileware_civicrm_wp_events_mb_public',  'event', 'side');
  add_meta_box('events_mb_id',      'Event ID',      '_agileware_civicrm_wp_events_mb_id',      'event', 'side');
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
