<?php
namespace MaddenMedia\KrakenEvents;

class Notifications {

    public static $admin_email = null;
    public static $admin_pages = [
        'new-submission'    => 'admin.php?page=kraken-events-submissions',
        'new-edits'         => 'admin.php?page=kraken-events-edits',
        'new-user'          => 'users.php?page=gf-pending-activations'
    ];

    /**
     * Initialize the class functionalities.
     */
    public static function init() {
        /* Set the notification email address to the admin email if a custom one has not been added */
        if (!get_option('kraken_events_notification_email')) {
            update_option('kraken_events_notification_email', get_option('admin_email'));
        }
        self::$admin_email = get_option('kraken_events_notification_email');
    }

    /**
     * Send an email notification to the admin upon user registration, submission, or edit.
     *
     * @param string $type Notification type (e.g., 'registration', 'submission', 'edit').
     * @param string $user_email User email address.
     * @param string $post_title Post title related to the notification.
     */
    public static function notify_admin($type, $user_email, $post_title) {

        $admin_url = get_admin_url(null, self::$admin_pages[$type]);

        //set the from email address
        $site_url = parse_url(get_bloginfo('url'), PHP_URL_HOST);
        $noreply_email = 'noreply@' . $site_url;

        $headers = array(
            'From: '.get_bloginfo('name').' <'.$noreply_email.'>',
            'Content-Type: text/html; charset=UTF-8'
        );

        switch ($type) {
            case "new-edits":
                $subject = "New partner edits at ".get_bloginfo('name');
                $message = "New event edits have been submitted by {$user_email} regarding {$post_title}. <a href=\"". esc_url($admin_url) ."\">Review event edits here</a>.";
                break;
            case "new-submission":
                $subject = "New partner event submission at ".get_bloginfo('name');
                $message = "A new event submission has been submitted by {$user_email} for {$post_title}. <a href=\"". esc_url($admin_url) ."\">Review new event submissions here</a>.";
                break;
            default:
                //Something went wrong.
                $subject = "";
                $message = "";
                Helpers::log_error($type.' - '.$user_email.' - '.$post_title);
                break;
        }

        //Helpers::log_error(self::$admin_email.' - '.$subject.' - '.$message);
        
        if (wp_mail(self::$admin_email, $subject, $message, $headers)) {
            //Helpers::log_error('Notification sent successfully.');
        } else {
            //Helpers::log_error('Error sending notification.');
        }
    }

    /**
     * Send an email notification to the partner
     *
     * @param string $type Notification type (e.g., 'registration', 'submission', 'edit').
     * @param string $user_email User email address.
     * @param string $post_title Post title related to the notification.
     */
    public static function notify_partner($type, $partner_email, $post_title) {

        //set the from email address
        $site_url = parse_url(get_bloginfo('url'), PHP_URL_HOST);
        $noreply_email = 'noreply@' . $site_url;

        $headers = array(
            'From: '.get_bloginfo('name').' <'.$noreply_email.'>',
            'Reply-To: '.get_bloginfo('name').' <'.self::$admin_email.'>',
            'Content-Type: text/html; charset=UTF-8'
        );

        switch ($type) {
            case "approve-new":
                $subject = "Your event submission has been approved at ".get_bloginfo('name');
                $message = "Your new event submission regarding {$post_title} has been approved and published.";
                break;
            case "approve-edits":
                $subject = "Your event changes has been approved at ".get_bloginfo('name');
                $message = "All pending event changes regarding {$post_title} have been approved.";
                break;
            case "deny-new":
                $subject = "Your event submission has been denied at ".get_bloginfo('name');
                $message = "Your new event submission regarding {$post_title} have been denied.";
                break;
            case "deny-edits":
                $subject = "Your event changes has been denied at ".get_bloginfo('name');
                $message = "All pending event changes regarding {$post_title} have been denied.";
                break;
            default:
                //Something went wrong.
                $subject = "";
                $message = "";
                Helpers::log_error($type.' - '.$partner_email.' - '.$post_title);
                break;
        }

        //Helpers::log_error($partner_email.' - '.$subject.' - '.$message);

        if (wp_mail($partner_email, $subject, $message, $headers)) {
            //Helpers::log_error('Notification sent successfully.');
        } else {
            //Helpers::log_error('Error sending notification.');
        }
    }
}