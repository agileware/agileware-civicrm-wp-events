<?php

function agileware_civicrm_wp_events_admin_init() {
  agileware_civicrm_wp_events_add_meta_boxes();
}

function agileware_civicrm_wp_events_add_meta_boxes(){
  add_meta_box('aa-event-time',    'Event Time',    '_agileware_civicrm_wp_aa_event_time',    'aa-event', 'normal');
  add_meta_box('aa-event-civicrm', 'CiviCRM Event', '_agileware_civicrm_wp_aa_event_civicrm', 'aa-event', 'side');
}

function agileware_civicrm_wp_events_save_post(){
  global $post;
  $post_id = $post->ID;

  $yy_s = $_POST['aa-event-start-yy'];
  $mm_s = $_POST['aa-event-start-mm'];
  $dd_s = $_POST['aa-event-start-dd'];
  $hh_s = $_POST['aa-event-start-hh'];
  $mn_s = $_POST['aa-event-start-mn'];
  $ss_s = $_POST['aa-event-start-ss'];
  $aa_event_start = $yy_s . $mm_s . $dd_s . $hh_s . $mn_s . $ss_s;
  update_post_meta($post_id, 'aa-event-start', $aa_event_start);

  $yy_e = $_POST['aa-event-end-yy'];
  $mm_e = $_POST['aa-event-end-mm'];
  $dd_e = $_POST['aa-event-end-dd'];
  $hh_e = $_POST['aa-event-end-hh'];
  $mn_e = $_POST['aa-event-end-mn'];
  $ss_e = $_POST['aa-event-end-ss'];
  $aa_event_end = $yy_e . $mm_e . $dd_e . $hh_e . $mn_e . $ss_e;
  update_post_meta($post_id, 'aa-event-end', $aa_event_end);

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

function _agileware_civicrm_wp_aa_event_time() {
  global $post;
  $custom = get_post_custom($post->ID);
  if (empty($custom)) {
    $curr_time = date('YmdHis');
    $mb_start = $curr_time;
    $mb_end = $curr_time;
  }
  else {
    $mb_start = $custom['aa-event-start'][0];
    $mb_end = $custom['aa-event-end'][0];
  }

  $time_start = strtotime($mb_start);
  $yy_start = date('Y', $time_start);
  $mm_start = date('m', $time_start);
  $dd_start = date('d', $time_start);
  $hh_start = date('H', $time_start);
  $mn_start = date('i', $time_start);
  $ss_start = date('s', $time_start);

  $time_end = strtotime($mb_end);
  $yy_end = date('Y', $time_end);
  $mm_end = date('m', $time_end);
  $dd_end = date('d', $time_end);
  $hh_end = date('H', $time_end);
  $mn_end = date('i', $time_end);
  $ss_end = date('s', $time_end);
  ?>
    <div class="wrap">
        <label>From</label>
      <p>
        <label>
          <span class="screen-reader-text">Day</span>
          <input type="text" id="aa-event-start-dd" name="aa-event-start-dd" value="<?php echo $dd_start;?>" size="2" maxlength="2" autocomplete="off">
        </label>
        <label>
          <span class="screen-reader-text">Month</span>
          <select id="aa-event-start-mm" name="aa-event-start-mm">
            <option value="01" data-text="Jan" <?php echo ("01"==$mm_start) ? 'selected="selected"' : ''?>>Jan</option>
            <option value="02" data-text="Feb" <?php echo ("02"==$mm_start) ? 'selected="selected"' : ''?>>Feb</option>
            <option value="03" data-text="Mar" <?php echo ("03"==$mm_start) ? 'selected="selected"' : ''?>>Mar</option>
            <option value="04" data-text="Apr" <?php echo ("04"==$mm_start) ? 'selected="selected"' : ''?>>Apr</option>
            <option value="05" data-text="May" <?php echo ("05"==$mm_start) ? 'selected="selected"' : ''?>>May</option>
            <option value="06" data-text="Jun" <?php echo ("06"==$mm_start) ? 'selected="selected"' : ''?>>Jun</option>
            <option value="07" data-text="Jul" <?php echo ("07"==$mm_start) ? 'selected="selected"' : ''?>>Jul</option>
            <option value="08" data-text="Aug" <?php echo ("08"==$mm_start) ? 'selected="selected"' : ''?>>Aug</option>
            <option value="09" data-text="Sep" <?php echo ("09"==$mm_start) ? 'selected="selected"' : ''?>>Sep</option>
            <option value="10" data-text="Oct" <?php echo ("10"==$mm_start) ? 'selected="selected"' : ''?>>Oct</option>
            <option value="11" data-text="Nov" <?php echo ("11"==$mm_start) ? 'selected="selected"' : ''?>>Nov</option>
            <option value="12" data-text="Dec" <?php echo ("12"==$mm_start) ? 'selected="selected"' : ''?>>Dec</option>
          </select>
        </label>
        <label>
          <span class="screen-reader-text">Year</span>
          <input type="text" id="aa-event-start-yy" name="aa-event-start-yy" value="<?php echo $yy_start;?>" size="4" maxlength="4" autocomplete="off">
        </label>
        @
        <label>
          <span class="screen-reader-text">Hour</span>
          <input type="text" id="aa-event-start-hh" name="aa-event-start-hh" value="<?php echo $hh_start;?>" size="2" maxlength="2" autocomplete="off">
        </label>
        :
        <label>
          <span class="screen-reader-text">Minute</span>
          <input type="text" id="aa-event-start-mn" name="aa-event-start-mn" value="<?php echo $mn_start;?>" size="2" maxlength="2" autocomplete="off">
        </label>
        :
        <label>
          <span class="screen-reader-text">Second</span>
          <input type="text" id="aa-event-start-ss" name="aa-event-start-ss" value="<?php echo $ss_start;?>" size="2" maxlength="2" autocomplete="off">
        </label>
      </p>
        <label>To</label>
      <p>
        <label>
          <span class="screen-reader-text">Day</span>
          <input type="text" id="aa-event-end-dd" name="aa-event-end-dd" value="<?php echo $dd_end;?>" size="2" maxlength="2" autocomplete="off">
        </label>
        <label>
          <span class="screen-reader-text">Month</span>
          <select id="aa-event-end-mm" name="aa-event-end-mm">
            <option value="01" data-text="Jan" <?php echo ("01"==$mm_end) ? 'selected="selected"' : ''?>>Jan</option>
            <option value="02" data-text="Feb" <?php echo ("02"==$mm_end) ? 'selected="selected"' : ''?>>Feb</option>
            <option value="03" data-text="Mar" <?php echo ("03"==$mm_end) ? 'selected="selected"' : ''?>>Mar</option>
            <option value="04" data-text="Apr" <?php echo ("04"==$mm_end) ? 'selected="selected"' : ''?>>Apr</option>
            <option value="05" data-text="May" <?php echo ("05"==$mm_end) ? 'selected="selected"' : ''?>>May</option>
            <option value="06" data-text="Jun" <?php echo ("06"==$mm_end) ? 'selected="selected"' : ''?>>Jun</option>
            <option value="07" data-text="Jul" <?php echo ("07"==$mm_end) ? 'selected="selected"' : ''?>>Jul</option>
            <option value="08" data-text="Aug" <?php echo ("08"==$mm_end) ? 'selected="selected"' : ''?>>Aug</option>
            <option value="09" data-text="Sep" <?php echo ("09"==$mm_end) ? 'selected="selected"' : ''?>>Sep</option>
            <option value="10" data-text="Oct" <?php echo ("10"==$mm_end) ? 'selected="selected"' : ''?>>Oct</option>
            <option value="11" data-text="Nov" <?php echo ("11"==$mm_end) ? 'selected="selected"' : ''?>>Nov</option>
            <option value="12" data-text="Dec" <?php echo ("12"==$mm_end) ? 'selected="selected"' : ''?>>Dec</option>
          </select>
        </label>
        <label>
          <span class="screen-reader-text">Year</span>
          <input type="text" id="aa-event-end-yy" name="aa-event-end-yy" value="<?php echo $yy_end;?>" size="4" maxlength="4" autocomplete="off">
        </label>
        @
        <label>
          <span class="screen-reader-text">Hour</span>
          <input type="text" id="aa-event-end-hh" name="aa-event-end-hh" value="<?php echo $hh_end;?>" size="2" maxlength="2" autocomplete="off">
        </label>
        :
        <label>
          <span class="screen-reader-text">Minute</span>
          <input type="text" id="aa-event-end-mn" name="aa-event-end-mn" value="<?php echo $mn_end;?>" size="2" maxlength="2" autocomplete="off">
        </label>
        :
        <label>
          <span class="screen-reader-text">Second</span>
          <input type="text" id="aa-event-end-ss" name="aa-event-end-ss" value="<?php echo $ss_end;?>" size="2" maxlength="2" autocomplete="off">
        </label>
      </p>
      <p id="aa-event-time-help">Event start and end times.</p>
    </div>
  <?php
}
