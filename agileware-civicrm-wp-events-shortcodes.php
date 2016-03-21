<?php

// Upcoming Events
// =============================================================================

function agileware_civicrm_wp_events_upcoming_events( $atts ) {
  extract( shortcode_atts( array(
    'id'          => '',
    'class'       => '',
    'style'       => '',
    'type'        => 'aa-event',
    'count'       => '',
    'category'    => '',
    'offset'      => '',
    'orientation' => '',
    'no_sticky'   => '',
    'no_image'    => '',
    'fade'        => ''
  ), $atts, 'aa_upcoming_events' ) );


  $type = 'aa-event';

  $id            = ( $id          != ''          ) ? 'id="' . esc_attr( $id ) . '"' : '';
  $class         = ( $class       != ''          ) ? 'x-recent-posts cf ' . esc_attr( $class ) : 'x-recent-posts cf';
  $style         = ( $style       != ''          ) ? 'style="' . $style . '"' : '';
  $count         = ( $count       != ''          ) ? $count : 3;
  $category      = ( $category    != ''          ) ? $category : '';
  $category_type = ( $type        == 'post'      ) ? 'category_name' : 'portfolio-category';
  $offset        = ( $offset      != ''          ) ? $offset : 0;
  $orientation   = ( $orientation != ''          ) ? ' ' . $orientation : ' horizontal';
  $no_sticky     = ( $no_sticky   == 'true'      );
  $no_image      = ( $no_image    == 'true'      ) ? $no_image : '';
  $fade          = ( $fade        == 'true'      ) ? $fade : 'false';

  $js_params = array(
    'fade' => ( $fade == 'true' )
  );

  $data = cs_generate_data_attributes( 'upcoming_events', $js_params );

  $output = "<div {$id} class=\"{$class}{$orientation}\" {$style} {$data} data-fade=\"{$fade}\" >";

    $q = new WP_Query( array(
      'orderby'             => 'meta_value',
      'post_type'           => "{$type}",
      'posts_per_page'      => "{$count}",
      'offset'              => "{$offset}",
      "{$category_type}"    => "{$category}",
      'ignore_sticky_posts' => $no_sticky,
      'meta_query' => array(
        array(
          'key' => 'aa-event-end',
          'value' => date('YmdHis'),
          'compare' => '>='
        )
      )
    ) );

    if ( $q->have_posts() ) : while ( $q->have_posts() ) : $q->the_post();

      if ( $no_image == 'true' ) {
        $image_output       = '';
        $image_output_class = 'no-image';
      } else {
        $image              = wp_get_attachment_image_src( get_post_thumbnail_id(), 'entry-cropped' );
        $bg_image           = ( $image[0] != '' ) ? ' style="background-image: url(' . $image[0] . ');"' : '';
        $image_output       = '<div class="x-recent-posts-img"' . $bg_image . '></div>';
        $image_output_class = 'with-image';
      }

      $event_start  = date('j F Y', strtotime(get_post_meta( get_the_ID(), 'aa-event-start', true )));

      $output .= '<a class="x-recent-post' . $count . ' ' . $image_output_class . '" href="' . get_permalink( get_the_ID() ) . '" title="' . esc_attr( sprintf( __( 'Permalink to: "%s"', csl18n() ), the_title_attribute( 'echo=0' ) ) ) . '">'
                 . '<article id="post-' . get_the_ID() . '" class="' . implode( ' ', get_post_class() ) . '">'
                   . '<div class="entry-wrap">'
                     . $image_output
                     . '<div class="x-recent-posts-content">'
                       . '<h3 class="h-recent-posts">' . get_the_title() . '</h3>'
                       . '<span class="x-recent-posts-date">' . $event_start . '</span>'
                     . '</div>'
                   . '</div>'
                 . '</article>'
               . '</a>';

    endwhile; endif; wp_reset_postdata();

  $output .= '</div>';

  return $output;
}

add_shortcode( 'aa_upcoming_events', 'agileware_civicrm_wp_events_upcoming_events' );
