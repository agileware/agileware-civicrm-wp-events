<?php

class AA_Upcoming_Events extends Cornerstone_Element_Base {

  public function data() {
    return array(
      'name'        => 'upcoming-events',
      'title'       => __('Upcoming Events', 'cornerstone' ),
      'section'     => 'content',
      'description' => __( 'Upcoming Events description.', 'cornerstone' ),
      'supports'    => array( 'id', 'class', 'style' )
    );
  }

  public function controls() {

    $this->addControl(
      'count',
      'select',
      __( 'Post Count', 'cornerstone' ),
      __( 'Select how many posts to display.', 'cornerstone' ),
      '2',
      array(
        'choices' => array(
          array( 'value' => '1', 'label' => __( '1', 'cornerstone' ) ),
          array( 'value' => '2', 'label' => __( '2', 'cornerstone' ) ),
          array( 'value' => '3', 'label' => __( '3', 'cornerstone' ) ),
          array( 'value' => '4', 'label' => __( '4', 'cornerstone' ) )
        )
      )
    );

    $this->addControl(
      'offset',
      'text',
      __( 'Offset', 'cornerstone' ),
      __( 'Enter a number to offset initial starting post of your Upcoming Events.', 'cornerstone' ),
      ''
    );

    $this->addControl(
      'category',
      'text',
      __( 'Category', 'cornerstone' ),
      __( 'To filter your events by category, enter in the slug of your desired category. To filter by multiple categories, enter in your slugs separated by a comma.', 'cornerstone' ),
      ''
    );

    $this->addControl(
      'orientation',
      'choose',
      __( 'Orientation', 'cornerstone' ),
      __( 'Select the orientation or your Upcoming Events.', 'cornerstone' ),
      'horizontal',
      array(
        'columns' => '2', 'choices' => array(
          array( 'value' => 'horizontal', 'label' => __( 'Horizontal', 'cornerstone' ), 'icon' => fa_entity( 'arrows-h' ) ),
          array( 'value' => 'vertical',   'label' => __( 'Vertical', 'cornerstone' ),   'icon' => fa_entity( 'arrows-v' ) )
        )
      )
    );

    $this->addControl(
      'no_sticky',
      'toggle',
      __( 'Ignore Sticky Posts', 'cornerstone' ),
      __( 'Select to ignore sticky posts.', 'cornerstone' ),
      true
    );

    $this->addControl(
      'no_image',
      'toggle',
      __( 'Remove Featured Image', 'cornerstone' ),
      __( 'Select to remove the featured image.', 'cornerstone' ),
      false
    );

    $this->addControl(
      'fade',
      'toggle',
      __( 'Fade Effect', 'cornerstone' ),
      __( 'Select to activate the fade effect.', 'cornerstone' ),
      false
    );

  }

  public function render( $atts ) {

    extract( $atts );

    $post_type = 'aa-event';
    $type = ( isset( $post_type ) ) ? 'type="' . $post_type . '"' : '';

    $shortcode = "[aa_upcoming_events $type count=\"$count\" offset=\"$offset\" category=\"$category\" orientation=\"$orientation\" no_sticky=\"$no_sticky\" no_image=\"$no_image\" fade=\"$fade\"{$extra}]";

    return $shortcode;

  }

}
