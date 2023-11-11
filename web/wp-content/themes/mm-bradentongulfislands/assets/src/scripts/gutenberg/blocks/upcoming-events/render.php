<?php

namespace MaddenNino\Blocks\UpcomingEvents;
use MaddenNino\Library\Constants as Constants;
  
/**
 * Render function for the dynamic example block
 * @param array $attrs        all block attributes
 * @param string $content     
 */
function render_block( $attrs, $content ) {

  $args = array(
      'posts_per_page' => -1
  );
  $events = eventastic_get_events($args);
  $eventCategories = eventastic_get_categories();

  $html = "<div class='" . Constants::BLOCK_CLASS . "-upcomingevents'>";

    $html .= '<img src="'.$attrs['defaultThumb'].'" alt="Events thumbnail" class="bgImg">';
  
    $html .= '<h2>'. $attrs['title'] .'</h2>';

    $html .= '<a href="'.$attrs['ctaURL'].'" class="events_btn">'.$attrs['ctaText'].'</a>';

    $html .= '<div class="eventsWrapper">';

      foreach($events as $index=>$upcoming):
        if($index < 3):
        $upcomingMeta = eventastic_get_event_meta($upcoming->ID);
        $date = strtotime($upcomingMeta['start_date']);
        if (date('Ymd', $date) < date('Ymd')) {
            if ($upcomingMeta['occurrences']) {
                foreach($upcomingMeta['occurrences'] as $occurence) {
                    if (date('Ymd', strtotime($occurence['start_date_time'])) >= date('Ymd')) {
                        $date = strtotime($occurence['start_date_time']);
                        break;
                    }
                }
            }
        }
        $html .= '<a href="'.get_the_permalink($upcoming->ID).'" class="eventPost">';

        // $html .= '<pre>'.print_r( $upcomingMeta, true ).'</pre>';

            $html .= '<div class="date">';
                $html .= '<span class="month">'.date('M', $date).'</span> ';
                $html .= '<span class="day">'.date('j', $date).'</span>';
            $html .= '</div>';

            $html .= '<div class="text">';
              $html .= '<h3>'.get_the_title($upcoming->ID).'</h3>';
              $html .= '<p class="location">'.$upcomingMeta['addr_multi'].'</p>';
            $html .= '</div>';
        $html .= '</a>';
        endif;
        endforeach;

    $html .= "</div>";

  $html .= "</div>";

  // $html .= "<pre>" . print_r( $attrs, true ) . "</pre>";

  return $html;
}