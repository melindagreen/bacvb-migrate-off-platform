<?php 

namespace MaddenNino\Library\Memberpress;

use MaddenNino\Library\Constants as C;
use WP_Query;

class MemberPressPortal {

    public static $account_actions;

	function __construct () {

        self::$account_actions = ['listings','events','add_listing','edit_event','add_event', 'edit_listing'];

		add_filter( 'mepr_account_nav_content', array(get_called_class(), 'nav_tabs'), 10, 1);
        add_action('mepr_enqueue_scripts', array(get_called_class(),'mepr_enqueue_scripts'), 10, 3);
        add_action( 'transition_post_status', array(get_called_class(),'post_status_notification'), 9, 3 );
        add_action('mepr_account_nav', array(get_called_class(),'mepr_add_some_tabs'));
        add_action( 'transition_post_status', array(get_called_class(),'handle_post_status'), 10, 3 );
        add_action( 'template_redirect', array(get_called_class(),'partner_portal_maintenance'), 10 );
        add_action( 'init', array(get_called_class(),'exclude_from_search'), 99 );
	}

  public static function partner_portal_maintenance() {

    $isMaintenance = get_field('partner_portal_maintenance', 12925);

    if(is_page( 'mepr-account' ) && $isMaintenance) {
      wp_redirect(site_url() . '/404?maintenance=true');
      exit;
    }
  }

    public static function exclude_from_search() {
      global $wp_post_types;

      if ( post_type_exists( 'memberpressgroup' ) ) {

          // exclude from search results
          $wp_post_types['memberpressgroup']->exclude_from_search = true;
      }
    }

    public static function mepr_add_some_tabs($action) {
        $support_active = (isset($_GET['action']) && $_GET['action'] == 'premium-support')?'mepr-active-nav-tab':'';
        ?>
          <span class="mepr-nav-item listing <?php echo $support_active; ?>">
            <a href="/account/?action=listings">Listings</a>
          </span>
          <span class="mepr-nav-item events <?php echo $support_active; ?>">
            <a href="/account/?action=events">Events</a>
          </span>
          <?php
    }

    public static function nav_tabs($action) {
        
         // Memberpress Account styles
         wp_enqueue_style(
            C::THEME_PREFIX . "-memberpress-account-css", // handle
            get_stylesheet_directory_uri()."/assets/build/memberpress-account.css", // src
            [], // dependencies
            null
        );

        // Retrieves template of defined account action
        if(in_array($_GET['action'], self::$account_actions)) {

            include get_stylesheet_directory() . '/library/memberpress/templates/account-'.$_GET['action'].'.php';
        }
    }

    public static function mepr_enqueue_scripts($is_product_page, $is_group_page, $is_account_page) {

        $assets_file = include(get_template_directory()."/assets/build/admin.asset.php" );
        // Memberpress Account script
        wp_enqueue_script(
            C::THEME_PREFIX . "-memberpress-account-js", // handle
            get_stylesheet_directory_uri()."/assets/build/memberpress-account.js", // src
            $assets_file["dependencies"], // dependencies
            $assets_file["version"], // version
            true // in footer?
        );
    }

    public static function post_status_notification ( $new_status, $old_status, $post ) {

        // Check if the post is transitioning from pending to publish
        if ( ($old_status === 'pending' && $new_status === 'publish') && ($post->post_type === 'event' || $post->post_type === 'listing') ) {
            // Get post author's email
            $author_id = $post->post_author;
            $author_email = get_the_author_meta( 'user_email', $author_id );
            $author_first_name = get_the_author_meta( 'first_name', $author_id );
    
            // Set up email parameters
            $to = $author_email;
            $subject = ucwords($post->post_type) .' Submission Review';
            $message = '<!doctype html>
            <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
              <head>
                <title>
                An update regarding your submission to Bradenton Gulf Islands.
                </title>
                <!--[if !mso]><!-->
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <!--<![endif]-->
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style type="text/css">
                  #outlook a {
                    padding:0;
                  }
                  body {
                    margin:0;
                    padding:0;
                    -webkit-text-size-adjust:100%;
                    -ms-text-size-adjust:100%;
                  }
                  table, td {
                    border-collapse:collapse;
                    mso-table-lspace:0pt;
                    mso-table-rspace:0pt;
                  }
                  img {
                    border:0;
                    height:auto;
                    line-height:100%;
                    outline:none;
                    text-decoration:none;
                    -ms-interpolation-mode:bicubic;
                  }
                  p {
                    display:block;
                    margin:13px 0;
                  }
                </style>
                <!--[if mso]>
            <noscript>
            <xml>
            <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
            </xml>
            </noscript>
            <![endif]-->
                <!--[if lte mso 11]>
            <style type="text/css">
            .mj-outlook-group-fix { width:100% !important; }
            </style>
            <![endif]-->
                <style type="text/css">
                  @media only screen and (min-width:480px) {
                    .mj-column-per-100 {
                      width:100% !important;
                      max-width: 100%;
                    }
                    .mj-column-px-400 {
                      width:400px !important;
                      max-width: 400px;
                    }
                  }
                </style>
                <style media="screen and (min-width:480px)">
                  .moz-text-html .mj-column-per-100 {
                    width:100% !important;
                    max-width: 100%;
                  }
                  .moz-text-html .mj-column-px-400 {
                    width:400px !important;
                    max-width: 400px;
                  }
                </style>
                <style type="text/css">
                  @media only screen and (max-width:479px) {
                    table.mj-full-width-mobile {
                      width: 100% !important;
                    }
                    td.mj-full-width-mobile {
                      width: auto !important;
                    }
                  }
                </style>
                <style type="text/css">
                </style>
              </head>
              <body style="word-spacing:normal;">
                <div
                     style=""
                     >
                  <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                  <div  style="margin:0px auto;max-width:600px;">
                    <table
                           align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"
                           >
                      <tbody>
                        <tr>
                          <td
                              style="direction:ltr;font-size:0px;padding:20px 0;padding-top:0px;text-align:center;"
                              >
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                            <div
                                 class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"
                                 >
                              <table
                                     border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%"
                                     >
                                <tbody>
                                  <tr>
                                    <td  style="vertical-align:top;padding-top:0px;">
                                      <table
                                             border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%"
                                             >
                                        <tbody>
                                          <tr>
                                            <td
                                                align="center" style="font-size:0px;padding:10px 25px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;word-break:break-word;"
                                                >
                                              <table
                                                     border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;"
                                                     >
                                                <tbody>
                                                  <tr>
                                                    <td  style="width:600px;">
                                                      <img
                                                           src="https://grapes.maddenmedia.com/wp-content/uploads/2024/04/top-logo.png" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="600" height="auto"
                                                           />
                                                    </td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                  <div  style="margin:0px auto;max-width:600px;">
                    <table
                           align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"
                           >
                      <tbody>
                        <tr>
                          <td
                              style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;"
                              >
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:400px;" ><![endif]-->
                            <div
                                 class="mj-column-px-400 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"
                                 >
                              <table
                                     border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"
                                     >
                                <tbody>
                                  <tr>
                                    <td
                                        align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;"
                                        >
                                      <div
                                           style="font-family:Helvetica Neue;font-size:14px;font-style:italic;line-height:1;text-align:left;color:#2b7b7c;"
                                           >
                                        <span id="ircq3">Hello, {{fname}}</span>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td
                                        align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;"
                                        >
                                      <div
                                           style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#525252;"
                                           >{{message}}
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td
                                        align="center" vertical-align="middle" style="font-size:0px;padding:10px 25px;word-break:break-word;"
                                        >
                                      <table
                                             border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate;line-height:100%;"
                                             >
                                        <tbody>
                                          <tr>
                                            <td
                                                align="center" bgcolor="#77c3c7" role="presentation" style="border:none;border-radius:25px 25px 25px 25px;cursor:auto;mso-padding-alt:10px 25px;background:#77c3c7;" valign="middle"
                                                >
                                              <a
                                                 href="{{listing_url}}" style="display:inline-block;background:#77c3c7;color:white;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;font-weight:normal;line-height:120%;margin:0;text-decoration:none;text-transform:none;padding:10px 25px;mso-padding-alt:0px;border-radius:25px 25px 25px 25px;" target="_blank"
                                                 >
                                                See your {{post_type}}
                                              </a>
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                  <div  style="margin:0px auto;max-width:600px;">
                    <table
                           align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"
                           >
                      <tbody>
                        <tr>
                          <td
                              style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:0px;padding-top:0px;text-align:center;"
                              >
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                            <div
                                 class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"
                                 >
                              <table
                                     border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"
                                     >
                                <tbody>
                                  <tr>
                                    <td
                                        align="center" style="font-size:0px;padding:10px 25px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;word-break:break-word;"
                                        >
                                      <table
                                             border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;"
                                             >
                                        <tbody>
                                          <tr>
                                            <td  style="width:600px;">
                                              <img
                                                   src="https://grapes.maddenmedia.com/wp-content/uploads/2024/04/bottom-wave.png" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="600" height="auto"
                                                   />
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <!--[if mso | IE]></td></tr></table><![endif]-->
                </div>
              </body>
            </html>';
            $results = "Hello {{fname}}, thanks for your submission! After review from our team, your {{post_type}} has been approved! Here’s a link to your {{post_type}} on our {{post_type}} page.
            We're excited to feature your {{post_type}} on our platform and look forward to its success! If you have any further questions or need assistance in making any changes or updates, feel free to reach out to emily.knight@bacvb.com.
            ";
            $original_post_id = get_post_meta($post->ID, 'original_post_id', true);
            $permalink = $original_post_id ? get_permalink($original_post_id) : get_permalink($post);
            $message = str_replace("{{message}}", $results, $message);
            $message = str_replace("{{fname}}", $author_first_name, $message);
            $message = str_replace("{{post_type}}", $post->post_type, $message);
            $message = str_replace("{{listing_url}}", $permalink, $message);

            $headers = array('Content-Type: text/html; charset=UTF-8');
            // Send email
            wp_mail($to, $subject, $message, $headers);
        }
        else if ( ($old_status === 'pending' && $new_status === 'draft') && ($post->post_type === 'event' || $post->post_type === 'listing') ) {
            // Get post author's email
            $author_id = $post->post_author;
            $author_email = get_the_author_meta( 'user_email', $author_id );
            $author_first_name = get_the_author_meta( 'first_name', $author_id );
    
            // Set up email parameters
            $to = $author_email;
            $subject = ucwords($post->post_type) .' Submission Review';
            $message = '<!doctype html>
            <html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
              <head>
                <title>
                An update regarding your submission to Bradenton Gulf Islands.
                </title>
                <!--[if !mso]><!-->
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <!--<![endif]-->
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style type="text/css">
                  #outlook a {
                    padding:0;
                  }
                  body {
                    margin:0;
                    padding:0;
                    -webkit-text-size-adjust:100%;
                    -ms-text-size-adjust:100%;
                  }
                  table, td {
                    border-collapse:collapse;
                    mso-table-lspace:0pt;
                    mso-table-rspace:0pt;
                  }
                  img {
                    border:0;
                    height:auto;
                    line-height:100%;
                    outline:none;
                    text-decoration:none;
                    -ms-interpolation-mode:bicubic;
                  }
                  p {
                    display:block;
                    margin:13px 0;
                  }
                </style>
                <!--[if mso]>
            <noscript>
            <xml>
            <o:OfficeDocumentSettings>
            <o:AllowPNG/>
            <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
            </xml>
            </noscript>
            <![endif]-->
                <!--[if lte mso 11]>
            <style type="text/css">
            .mj-outlook-group-fix { width:100% !important; }
            </style>
            <![endif]-->
                <style type="text/css">
                  @media only screen and (min-width:480px) {
                    .mj-column-per-100 {
                      width:100% !important;
                      max-width: 100%;
                    }
                    .mj-column-px-400 {
                      width:400px !important;
                      max-width: 400px;
                    }
                  }
                </style>
                <style media="screen and (min-width:480px)">
                  .moz-text-html .mj-column-per-100 {
                    width:100% !important;
                    max-width: 100%;
                  }
                  .moz-text-html .mj-column-px-400 {
                    width:400px !important;
                    max-width: 400px;
                  }
                </style>
                <style type="text/css">
                  @media only screen and (max-width:479px) {
                    table.mj-full-width-mobile {
                      width: 100% !important;
                    }
                    td.mj-full-width-mobile {
                      width: auto !important;
                    }
                  }
                </style>
                <style type="text/css">
                </style>
              </head>
              <body style="word-spacing:normal;">
                <div
                     style=""
                     >
                  <!--[if mso | IE]><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                  <div  style="margin:0px auto;max-width:600px;">
                    <table
                           align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"
                           >
                      <tbody>
                        <tr>
                          <td
                              style="direction:ltr;font-size:0px;padding:20px 0;padding-top:0px;text-align:center;"
                              >
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                            <div
                                 class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"
                                 >
                              <table
                                     border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%"
                                     >
                                <tbody>
                                  <tr>
                                    <td  style="vertical-align:top;padding-top:0px;">
                                      <table
                                             border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%"
                                             >
                                        <tbody>
                                          <tr>
                                            <td
                                                align="center" style="font-size:0px;padding:10px 25px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;word-break:break-word;"
                                                >
                                              <table
                                                     border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;"
                                                     >
                                                <tbody>
                                                  <tr>
                                                    <td  style="width:600px;">
                                                      <img
                                                           src="https://grapes.maddenmedia.com/wp-content/uploads/2024/04/top-logo.png" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="600" height="auto"
                                                           />
                                                    </td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                  <div  style="margin:0px auto;max-width:600px;">
                    <table
                           align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"
                           >
                      <tbody>
                        <tr>
                          <td
                              style="direction:ltr;font-size:0px;padding:20px 0;text-align:center;"
                              >
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:400px;" ><![endif]-->
                            <div
                                 class="mj-column-px-400 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"
                                 >
                              <table
                                     border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"
                                     >
                                <tbody>
                                  <tr>
                                    <td
                                        align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;"
                                        >
                                      <div
                                           style="font-family:Helvetica Neue;font-size:14px;font-style:italic;line-height:1;text-align:left;color:#2b7b7c;"
                                           >
                                        <span id="ircq3">Hello, {{fname}}</span>
                                      </div>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td
                                        align="left" style="font-size:0px;padding:10px 25px;word-break:break-word;"
                                        >
                                      <div
                                           style="font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:13px;line-height:1;text-align:left;color:#525252;"
                                           >{{message}}
                                      </div>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <!--[if mso | IE]></td></tr></table><table align="center" border="0" cellpadding="0" cellspacing="0" class="" role="presentation" style="width:600px;" width="600" ><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;"><![endif]-->
                  <div  style="margin:0px auto;max-width:600px;">
                    <table
                           align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"
                           >
                      <tbody>
                        <tr>
                          <td
                              style="direction:ltr;font-size:0px;padding:20px 0;padding-bottom:0px;padding-top:0px;text-align:center;"
                              >
                            <!--[if mso | IE]><table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:600px;" ><![endif]-->
                            <div
                                 class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;"
                                 >
                              <table
                                     border="0" cellpadding="0" cellspacing="0" role="presentation" style="vertical-align:top;" width="100%"
                                     >
                                <tbody>
                                  <tr>
                                    <td
                                        align="center" style="font-size:0px;padding:10px 25px;padding-top:0px;padding-right:0px;padding-bottom:0px;padding-left:0px;word-break:break-word;"
                                        >
                                      <table
                                             border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;"
                                             >
                                        <tbody>
                                          <tr>
                                            <td  style="width:600px;">
                                              <img
                                                   src="https://grapes.maddenmedia.com/wp-content/uploads/2024/04/bottom-wave.png" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;" width="600" height="auto"
                                                   />
                                            </td>
                                          </tr>
                                        </tbody>
                                      </table>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                            <!--[if mso | IE]></td></tr></table><![endif]-->
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                  <!--[if mso | IE]></td></tr></table><![endif]-->
                </div>
              </body>
            </html>';
            $results = "Hello {{fname}}, thanks for your submission! After review from our team, we regret to inform you that your {{post_type}} has been declined for the following reason(s):
                <br>
                {{reason}}
                <br>
                If you have any questions or need further clarification, please don’t hesitate to reach out to emily.knight@bacvb.com.
                ";
            $reason = get_field('submission_note', $post->ID) !== null ? get_field('submission_note', $post->ID) : '';
            $message = str_replace("{{message}}", $results, $message);
            $message = str_replace("{{reason}}", $reason, $message);
            $message = str_replace("{{reason}}", '', $message);
            $message = str_replace("{{fname}}", $author_first_name, $message);
            $message = str_replace("{{post_type}}", $post->post_type, $message);
            $message = str_replace("{{listing_url}}", get_permalink($post), $message);

            $headers = array('Content-Type: text/html; charset=UTF-8');
            // Send email
            wp_mail($to, $subject, $message, $headers);
        }
    }

    public static function handle_post_status($new_status, $old_status, $post) {

       if($post->post_type === 'event' || $post->post_type === 'listing') {

            if ($old_status === 'pending' && $new_status === 'publish') {

                $original_post_id = get_post_meta($post->ID, 'original_post_id', true);

                if ($original_post_id) {
                    // Get the original post data
                    $original_post = get_post($original_post_id);

                    // Copy all post data to the post with original_post_id
                    $post_data = array(
                        'ID' => $original_post_id,
                        'post_title' => $post->post_title,
                        'post_content' => $post->post_content,
                        'post_excerpt' => $post->post_excerpt,
                        'post_status' => 'publish'
                    );

                    // Update the post with original_post_id with the data from the original post
                    wp_update_post($post_data);

                    // Transfer all metadata from original post to the new post
                    $post_meta = get_post_meta($post->ID);
                    foreach ($post_meta as $meta_key => $meta_values) {
                        foreach ($meta_values as $meta_value) {
                            update_post_meta($original_post_id, $meta_key, $meta_value);
                        }
                    }

                    // Remove cloned_post_id meta data
                    delete_post_meta($original_post_id, 'cloned_post_id');

                    // Remove original_post_id post meta from the original post
                    delete_post_meta($post->ID, 'original_post_id');

                    // Delete the original post
                    wp_delete_post($post->ID, true); // Set the second parameter to true to bypass trash
                }

            }

            else if ($old_status === 'pending' && $new_status === 'draft') {
                $original_post_id = get_post_meta($post->ID, 'original_post_id', true);
                if ($original_post_id) {

                    wp_update_post(array(
                        'ID' => $post->ID,
                        'post_status' => 'trash' 
                    ));
                }
            }

            //Prevents post status from changing to draft
            else if($old_status === 'pending' && $new_status === 'trash') {
                
            // If post is moved to trash, change its title
            $new_title = '[REJECTED] ' . $post->post_title;
            wp_update_post(array(
                'ID' => $post->ID,
                'post_title'   => $new_title
            ));
            
            // Get original_post_id
            $original_post_id = get_post_meta($post->ID, 'original_post_id', true);
            if ($original_post_id) {
           
                // Remove cloned_post_id meta data
                delete_post_meta($original_post_id, 'cloned_post_id');
                
                // Remove original_post_id post meta from the first post
                delete_post_meta($post->ID, 'original_post_id');
            }

            }
        }
    }
}