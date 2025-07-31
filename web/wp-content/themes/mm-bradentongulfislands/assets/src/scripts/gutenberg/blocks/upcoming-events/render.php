<?php

namespace MaddenNino\Blocks\UpcomingEvents;
use MaddenNino\Library\Constants as Constants;
  
$attrs = $attributes;

$args = array(
    'posts_per_page' => -1,
    'start_date' => date('Y-m-d')
);
$events = eventastic_get_events($args);
$eventCategories = eventastic_get_categories();

$html = "<div class='" . Constants::BLOCK_CLASS . "-upcomingevents'>";

  $html .= '<div class="title"><h2>'. $attrs['title'] .'</h2></div>';

  $html .= $content;

  $html .= '<div class="eventsWrapper">';

    foreach($events as $index=>$upcoming):
      if($index < 3):
      $upcomingMeta = eventastic_get_event_meta($upcoming->ID);
      $startDate = strtotime($upcomingMeta['start_date']);
      $endDate = strtotime($upcomingMeta['end_date']);

      if (date('Ymd', $startDate) < date('Ymd')) {
          if (isset($upcomingMeta['occurrences']) && is_array($upcomingMeta['occurrences'])) {
              foreach($upcomingMeta['occurrences'] as $occurence) {
                  if (date('Ymd', strtotime($occurence['start_date_time'])) >= date('Ymd')) {
                      $startDate = strtotime($occurence['start_date_time']);
                      break;
                  }
              }
          }
      }
      $html .= '<a href="'.get_the_permalink($upcoming->ID).'" class="eventPost">';
      // $html .= '<pre>'.print_r( $upcomingMeta, true ).'</pre>';

          $html .= '<div class="date">';
            $html .= '<span class="">'.ltrim(date('m/d', $startDate), '0').' - '.ltrim(date('m/d', $endDate),'0').'</span>';
          $html .= '</div>';

          $html .= '<div class="text">';
            $html .= '<h3>'.get_the_title($upcoming->ID).'</h3>';
            $html .= '<p class="location">'.$upcomingMeta['addr_multi'].'</p>';
          $html .= '</div>';
      $html .= '</a>';
      endif;
      endforeach;

      $html .= '<a href="/events" class="viewBtn">View All Events</a>';

  $html .= "</div>"; // .eventsWrapper

$html .= "</div>";

// $html .= "<pre>" . print_r( $content, true ) . "</pre>";

echo $html;
