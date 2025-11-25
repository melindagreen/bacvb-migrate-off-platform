<?php /**
 * Constants for PartnerPortal
 */

namespace PartnerPortal\Library;

class Constants {
    // General plugin prefix
    const PLUGIN_PREFIX = "mm";
    const PLUGIN_DIR_NAME = "partner-portal";


    public const PLUGIN_NAME_SINGULAR = "Listing";
    public const PLUGIN_NAME_PLURAL = "Listings";
    public const CPT_NAME_SINGULAR = "Listing";
    public const CPT_NAME_PLURAL = "Listings";

    // Settings slugs
    public const PLUGIN_MENU_ADMIN_LABEL = "PartnerPortal";

    const PLUGIN_ADMIN_PAGE_TITLE = "Partner Portal";
    const PLUGIN_ADMIN_MENU_TITLE = "Partner Portal";
    const PLUGIN_ADMIN_MENU_SLUG = "partner_portal_options";
    const PLUGIN_SETTING_SLUG = "plugin:listings";
    const PLUGIN_SETTING_GROUP_SLUG = "plugin:listings_group";

    const PLUGIN_THEME_DIR_SLUG = "partnerportal-theme-files";


    // CPT and taxonomies
    //
    // FUTURE Having a custom field class specified here is not a lot of help,
    //  because you still have to go make that class if you add a taxonomy here. That said,
    //  we aren't likely to need to add random taxonomies, either. We also have to refer
    //  to them by their key in code, meaning they aren't a true constant.
    public const PLUGIN_DEFAULT_CUSTOM_POST_TYPE = "listing";

    public const CPT_TAXONOMIES = array(
        "listing_categories" => array(
            "single" => "Category", 
            "plural" => "Categories", 
            "showAdminColumn" => true
        ),
    );    

    // Security keys
    public const NONCE_ROOT = "nonce-partnerportal-";

    public const ADMIN_CUSTOM_POST_TYPE_MENU_POS = 27;
    public const WP_POST_META_KEY_PREPEND = "partnerportal_";

    public const DATETIME_DASH_FORMAT_IN = "Y-m-d H:i:s";
    public const DATE_DASH_FORMAT_IN = "m-d-Y";
    public const DATETIME_SLASH_FORMAT_IN = "m/d/Y H:i:s";
    public const DATETIME_SLASH_AMPM_FORMAT_IN = "m/d/Y H:i:s A";
    public const DATE_SLASH_FORMAT_IN = "m/d/Y";
    public const DATE_FORMAT_MYSQL = "Y-m-d";
    public const DATETIME_FORMAT_MYSQL = "Y-m-d H:i:s";
    public const DATE_FORMAT_PURDY = "F d, Y";
    public const DATE_FORMAT_PURDY_NO_YEAR = "F j";
    public const DATE_FORMAT_JS_DATEPICKER = "M/D/YYYY";
    public const DATE_FORMAT_JS_MYSQL = "YYYY-MM-DD";
    public const TIME_FORMAT_JS_PRETTY = "h:mm a";
    public const TIME_FORMAT_JS_MYSQL = "HH:mm:ss";

    public const SELECT_STATES = array(
        '' => '',
        'AL' => 'Alabama',
        'AK' => 'Alaska',
        'AZ' => 'Arizona',
        'AR' => 'Arkansas',
        'CA' => 'California',
        'CO' => 'Colorado',
        'CT' => 'Connecticut',
        'DE' => 'Delaware',
        'DC' => 'District of Columbia',
        'FL' => 'Florida',
        'GA' => 'Georgia',
        'HI' => 'Hawaii',
        'ID' => 'Idaho',
        'IL' => 'Illinois',
        'IN' => 'Indiana',
        'IA' => 'Iowa',
        'KS' => 'Kansas',
        'KY' => 'Kentucky',
        'LA' => 'Louisiana',
        'ME' => 'Maine',
        'MD' => 'Maryland',
        'MA' => 'Massachusetts',
        'MI' => 'Michigan',
        'MN' => 'Minnesota',
        'MS' => 'Mississippi',
        'MO' => 'Missouri',
        'MT' => 'Montana',
        'NE' => 'Nebraska',
        'NV' => 'Nevada',
        'NH' => 'New Hampshire',
        'NJ' => 'New Jersey',
        'NM' => 'New Mexico',
        'NY' => 'New York',
        'NC' => 'North Carolina',
        'ND' => 'North Dakota',
        'OH' => 'Ohio',
        'OK' => 'Oklahoma',
        'OR' => 'Oregon',
        'PA' => 'Pennsylvania',
        'RI' => 'Rhode Island',
        'SC' => 'South Carolina',
        'SD' => 'South Dakota',
        'TN' => 'Tennessee',
        'TX' => 'Texas',
        'UT' => 'Utah',
        'VT' => 'Vermont',
        'VA' => 'Virginia',
        'WA' => 'Washington',
        'WV' => 'West Virginia',
        'WI' => 'Wisconsin',
        'WY' => 'Wyoming',
    );
        

}