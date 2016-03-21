<?php

class AA_Upcoming_Events extends Cornerstone_Element_Base {

  public function data() {
    return array(
      'name'        => 'upcoming-events',
      'title'       => __('Upcoming Events', csl18n() ),
      'section'     => 'content',
      'description' => __( 'Upcoming Events description.', csl18n() ),
      'supports'    => array( 'id', 'class', 'style' )
    );
  }

  public function controls() {

    $this->addControl(
      'count',
      'select',
      __( 'Post Count', csl18n() ),
      __( 'Select how many posts to display.', csl18n() ),
      '2',
      array(
        'choices' => array(
          array( 'value' => '1', 'label' => __( '1', csl18n() ) ),
          array( 'value' => '2', 'label' => __( '2', csl18n() ) ),
          array( 'value' => '3', 'label' => __( '3', csl18n() ) ),
          array( 'value' => '4', 'label' => __( '4', csl18n() ) )
        )
      )
    );

    $this->addControl(
      'offset',
      'text',
      __( 'Offset', csl18n() ),
      __( 'Enter a number to offset initial starting post of your Upcoming Events.', csl18n() ),
      ''
    );

    $this->addControl(
      'category',
      'text',
      __( 'Category', csl18n() ),
      __( 'To filter your events by category, enter in the slug of your desired category. To filter by multiple categories, enter in your slugs separated by a comma.', csl18n() ),
      ''
    );

    $this->addControl(
      'orientation',
      'choose',
      __( 'Orientation', csl18n() ),
      __( 'Select the orientation or your Upcoming Events.', csl18n() ),
      'horizontal',
      array(
        'columns' => '2', 'choices' => array(
          array( 'value' => 'horizontal', 'label' => __( 'Horizontal', csl18n() ), 'icon' => fa_entity( 'arrows-h' ) ),
          array( 'value' => 'vertical',   'label' => __( 'Vertical', csl18n() ),   'icon' => fa_entity( 'arrows-v' ) )
        )
      )
    );

    $this->addControl(
      'no_sticky',
      'toggle',
      __( 'Ignore Sticky Posts', csl18n() ),
      __( 'Select to ignore sticky posts.', csl18n() ),
      true
    );

    $this->addControl(
      'no_image',
      'toggle',
      __( 'Remove Featured Image', csl18n() ),
      __( 'Select to remove the featured image.', csl18n() ),
      false
    );

    $this->addControl(
      'fade',
      'toggle',
      __( 'Fade Effect', csl18n() ),
      __( 'Select to activate the fade effect.', csl18n() ),
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
