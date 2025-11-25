<?php
/*
 Template Name: Listings Management
 */
use MaddenNino as nino; 
if ( have_posts() ) : ?>
    <?php while ( have_posts() ) {
        $show_title = 1;
        $create_hero = 1;
        $show_breadcrumb = 1;
        $show_debug = 1;
        $main_no_padding = 0;
        $content_class_wrapper = "";
        $blocks = parse_blocks($post->post_content);
        if( is_array( $blocks) && count($blocks)> 0 ){
            $first_block = $blocks[0];
            if( 'core/cover' == $first_block['blockName'] ){
                $show_title = 0;
                $create_hero = 0;
            }
            if( 'mmnino/mmslideshow' == $first_block['blockName'] ){
                $show_title = 0;
                $create_hero = 0;
                $show_breadcrumb = 0;
                $main_no_padding = 1;
                $content_class_wrapper = " has-slideshow-hero";
            }
        }
    ?>
<?php get_header(); ?>
<style>
    table.greyGridTable {  width: 100%;
  text-align: center;
  border-collapse: collapse;
}
table.greyGridTable td, table.greyGridTable th {
  //border: 1px solid #FFFFFF;
  padding: 3px 4px;
}
table.greyGridTable tbody td {
  font-size: 13px;
  color: black;
  opacity: 1 !important;
}
table.greyGridTable tbody > tr:nth-child(3n) > td {
    padding-bottom: 2rem;
    border-bottom: solid 1px grey;
}
table.greyGridTable td:nth-child(even) {
  background: #EBEBEB;
}
table.greyGridTable .sub-post-tr thead {
  background: #FFFFFF;
}
table.greyGridTable thead th {
  font-size: 15px;
  font-weight: bold;
  color: #333333;
  text-align: center;
}
table.greyGridTable thead th:first-child {
  //border-left: none;
}

table.greyGridTable tfoot {
  font-size: 14px;
  font-weight: bold;
  color: #333333;
  //border-top: 4px solid #333333;
}
table.greyGridTable tfoot td {
  font-size: 14px;
}
</style>
<h1>test</h1>
    <?php
        if( $show_title ){
            // communicate to the cover block that a title has been shown
            $post->rendered_header = 1;
        }
        if( $create_hero && get_post_thumbnail_id() ){
            echo '
            <div class="wp-block-cover-wrapper">
                <div class="wp-block-cover" data-extra="true">
                    <span aria-hidden="true" class="has-transparent-background-color has-background-dim-40 wp-block-cover__gradient-background has-background-dim"></span>';
            the_post_thumbnail('full',['class'=> 'wp-block-cover__image-background']);
            if( $show_title ){
                get_template_part('title','',['wrapper_type' => 'inner']);
            }
            echo '</div></div>';
        }
        if( $create_hero && ( !get_post_thumbnail_id() || $show_title) ){
            get_template_part('title','',['wrapper_type' => 'main']);
            if( $show_debug ){
                echo '<!-- title generated on page.php because no featured image AND has core/cover as first block-->';
            }
        }
        if( 1 != 1 && $create_hero && $show_title ){
            //echo "<h1>" . get_the_title() . "</h1>";
            if( $show_debug ){
                echo '<!-- title generated on page.php because there is no core/cover as first block-->';
            }
        }
        the_post(); 
        echo "<div class='hermann-content-wrapper " . $content_class_wrapper . "'>";

        the_content();
        echo '<div class="hermann-content-wrapper" style="text-align:center;">
        <a class="hermann-btn ribbon arrow" href="?download=1" style="margin:0 auto; display:inline-block; margin-bottom:4rem;"><div>Download Partner Info</div></a>
</div>';
$args = [
    'post_type' => ['listing','event'],
    'post_status' => array('publish', 'pending', 'draft'),
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'partner_changes',
            'value' => 'changes',
            'compare' => 'LIKE',
        )
    )
];
$query = new WP_Query($args);
//print_r($query);
if( $query->posts ){
    $output = "
    <h3>New Submissions and Revisions</h3>
    <table class='greyGridTable changesTable'>
        <thead>
            <tr>
                <th></th>
            </tr>
        <thead>
    <tbody>";

    foreach( $query->posts as $listing ){
$user = get_user_by( 'id', $listing->post_author );
$username = $user->data->user_login;
//$user_name = $user
        $post_history = json_decode( get_post_meta($listing->ID, 'partner_changes', true) );
        if( 1== 1 || is_array($post_history)){
    $output .= "
        <tr class='post-tr'>
            <td style='font-size:1.25rem;font-style:bold;'>". $listing->post_title. " by <i>" . $username ."</i> </td>
        </tr>
        <tr class='post-tr'>
            <td colspan=''>
                <button class='un-button view-changes'>View Changes</button>
                <a class='listings-btn' target='_blank' href='" . get_edit_post_link($listing->ID) . "'>Review and Publish</a>
            </td>
        </tr>
        <tr class='post-tr sub-post-tr'>

            <td colspan=''>
                <table class='gvreyGridTable subtable'>
                    <thead>
                        <tr>
                            <th>Field Changed</th>
                            <th>Old Value</th>
                            <th>New Value</th>
                            <th>Date</th>
                        </tr>
                    <thead>
                <tbody>";
            if( property_exists($post_history, 'changes') && ( is_array($post_history->changes) || is_object($post_history->changes) ) ){
                foreach( $post_history->changes as $date => $change_array ){
                    foreach( $change_array as $field_changed => $meta_change ){
                        if( 'is_new_post' == $field_changed ){
                            $output .= "<tr><td colspan='4'><b>New Post Submitted</b></td></tr>";
                        }
                        else{
                            $exclude = [
                                '_edit_last'
                            ];
                            if( !in_array($field_changed,$exclude)){    
                                $field_name = preg_replace("#partnerportal#","",$field_changed);
                                $field_name = preg_replace("#_#"," ",$field_name);
                                $output .= "<tr>
                                    <td style='white-space:no-wrap'>". ucwords($field_name) ."</td>
                                    <td>".$meta_change->old ."</td>
                                    <td>".$meta_change->new ."</td>
                                    <td>".date('m/d/Y',$date) ."</td>
                                </tr>";            
                            }
                        }
                    }
                }
            }
        }
        $output .= "</tbody></table></td></tr>";
    }
    $output .= "</tbody></table>";
}
else{
    $output .= "<div class='wp-block-cover-wrapper align_center' style='margin-bottom:10rem'>
        <h3 style='color:#AB4732; margin-top:3rem !important;'>Grab a tea and relax...</h3><h4>all the Listings and Events have been reviewed and published.</h4>
    </div>";
}
echo $output;
//print_r($query->posts);

            echo "</div>";
    } ?>
<?php endif; ?>
<?php get_footer(); ?>