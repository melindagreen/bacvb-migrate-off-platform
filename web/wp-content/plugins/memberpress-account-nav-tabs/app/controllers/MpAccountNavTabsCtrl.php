<?php
if ( ! defined( 'ABSPATH' ) ) {
  die( 'You are not allowed to call this page directly.' );
}

class MpAccountNavTabsCtrl extends MeprBaseCtrl {

  public function __construct() {
    parent::__construct();
  }

  /**
   * Load hooks.
   *
   * @return void
   */
  public function load_hooks() {
    add_filter( 'mepr_view_paths', array( $this, 'add_view_path' ) );
    add_action( 'mepr_display_account_options', array( $this, 'display_options' ) );
    add_action( 'mepr-process-options', array( $this, 'save_options' ) );
    add_action( 'wp_ajax_add_new_tab_form', array( $this, 'add_new_tab_form' ) );

    // Enqueue scripts
    add_action( 'mepr-options-admin-enqueue-script', array( $this, 'admin_enqueue_options_scripts' ) );

    // Front end
    add_action( 'mepr_account_nav', array( $this, 'display_nav_tabs' ) );
    add_action( 'mepr_account_nav_content', array( $this, 'display_nav_tab_content' ) );

    // BuddyPress + MemberPress
    add_action( 'bp_setup_nav', array( $this, 'setup_bp_nav' ), 11 );
    add_action( 'plugins_loaded', array( $this, 'maybe_redirect_bp_tabs' ) );

    // Add Nav tab rule before partial rule
    add_filter( 'mepr-rule-types-before-partial', array( $this, 'nav_tab_rule_type' ) );
    add_filter( 'mepr-rule-contents-array', array( $this, 'nav_tab_contents_array' ), 10, 2 );
    add_filter( 'mepr-rule-content', array( $this, 'nav_tab_content' ), 10, 3 );
    add_filter( 'mepr-rule-has-content', array( $this, 'has_nav_tab' ), 10, 2 );
    add_filter( 'mepr-rule-search-content', array( $this, 'find_nav_tabs' ), 10, 3 );
    add_filter( 'mepr-extend-rules', array( $this, 'extend_rules' ), 10, 3 );

    // Add tab IDs if not found after accessing the dashboard
    add_action( 'admin_init', array( $this, 'add_tabs_ids' ) );

  }

  /**
   * Add plugin path to memberpress view path
   *
   * @param mixed $paths MemberPress paths
   *
   * @return mixed
   */
  function add_view_path( $paths ) {
    array_splice( $paths, 1, 0, MACCONTNAVTABS_APP . 'views' );

    return $paths;
  }

  public function display_options() {
    $is_enabled = get_option( 'mepr_account_nav_tabs_enabled', false );
    $no_tabs    = true;
    MeprView::render( '/admin/options/account-nav-tabs', get_defined_vars() );
  }

  public function save_options( $params ) {
    update_option( 'mepr_account_nav_tabs_enabled', (int) ( isset( $params['mepr_account_nav_tabs_enabled'] ) ) );

    // The max ID in old tabs
    $max_id = self::max_id_checker();

    $tabs = array();

    if ( isset( $_POST['mepr_account_nav_tab'] ) ) {
      foreach ( $_POST['mepr_account_nav_tab'] as $tab ) {
        if ( $tab['type'] == 'url' && empty( $tab['url'] ) ) {
          continue;
        }
        if ( $tab['type'] == 'content' && empty( $tab['content'] ) ) {
          continue;
        }

        $tabs[] = array(
          'tab_id'     => ( ! empty( $tab['tab_id'] ) ) ? (int) $tab['tab_id'] : (int) ++$max_id,
          'title'      => stripslashes( $tab['title'] ),
          'type'       => stripslashes( $tab['type'] ),
          'url'        => stripslashes( $tab['url'] ),
          'new_tab'    => ( isset( $tab['new_tab'] ) ) ? 1 : 0,
          'content'    => stripslashes( $tab['content'] )
        );

      }
    }
      update_option( 'mepr_account_nav_tabs', $tabs );
  }

  public function admin_enqueue_options_scripts( $hook ) {
    wp_enqueue_style( 'mp-accountnavtabs-options-css', MACCONTNAVTABS_URL . '/css/options.css', array() );
    $helpers = array(
      'wpnonce'       => wp_create_nonce( MACCONTNAVTABS_SLUG ),
      'confirmDelete' => __( 'Are you sure you want to delete this tab?', 'memberpress-account-nav-tabs' ),
    );
    wp_enqueue_script( 'mp-accountnavtabs-options-js', MACCONTNAVTABS_URL . '/js/options.js' );
    wp_localize_script( 'mp-accountnavtabs-options-js', 'MeprAccountNavTabs', $helpers );
  }

  public static function get_tabs() {
    $tabs       = array();
    $saved_tabs = get_option( 'mepr_account_nav_tabs', false );
    if ( $saved_tabs === false ) {
      return false;
    }
    foreach ( $saved_tabs as $tab ) {
      if ( ! is_object( $tab ) ) {
        $tabs[] = (object) $tab;
      } else {
        $tabs[] = $tab;
      }
    }

    return $tabs;
  }

  public function add_new_tab_form() {
    ob_start();
    $random_id = (int) rand( 100, 100000 );
    MpAccountNavTabsHelper::render_tab( $random_id, __( 'Tab Title', 'memberpress-account-nav-tabs' ), 'content', '', '', '', '' );
    $tab = ob_get_clean();
    die( trim( $tab ) );
  }

  public function display_nav_tabs() {
    $is_enabled = (bool) get_option( 'mepr_account_nav_tabs_enabled', false );
    if ( ! $is_enabled ) {
      return;
    }
    $tabs = self::get_tabs();

    if ( empty( $tabs ) ) {
      return;
    }

    $uri_path = explode( '?', $_SERVER['REQUEST_URI'], 2 );

    // Get User Info
    $user = MeprUtils::get_currentuserinfo();

    foreach ( $tabs as $i => $tab ) {
      // Apply Tab Nav Rules
      if ( ! MeprUtils::is_mepr_admin() && MeprRule::is_locked_for_user( $user, $tab->tab_id ) ) {
        continue;
      }

      $active  = ( isset( $_GET['action'] ) && $_GET['action'] == 'tab' . $i ) ? 'mepr-active-nav-tab' : '';
      $new_tab = ( $tab->new_tab ) ? 'target="_blank"' : '';
      if ( $tab->type === 'content' ) { ?>
        <span class="mepr-nav-item <?php echo $active; ?>">
          <a href="<?php echo $uri_path[0]; ?>?action=tab<?php echo $i; ?>"><?php echo stripslashes( $tab->title ); ?></a>
        </span>
        <?php
      } else if ( $tab->type === 'url' ) { ?>
        <span class="mepr-nav-item">
          <a href="<?php echo stripslashes( $tab->url ); ?>" <?php echo $new_tab; ?>><?php echo stripslashes( $tab->title ); ?></a>
        </span>
        <?php
      }
    }
  }

  public function display_nav_tab_content( $action ) {
    $is_enabled = (bool) get_option( 'mepr_account_nav_tabs_enabled', false );
    if ( ! $is_enabled ) {
      return;
    }
    $tabs = self::get_tabs();

    if ( empty( $tabs ) ) {
      return;
    }

    // Get User Info
    $user = MeprUtils::get_currentuserinfo();

    foreach ( $tabs as $i => $tab ) {
      // Apply Tab Nav Rules
      if ( ! MeprUtils::is_mepr_admin() && MeprRule::is_locked_for_user( $user, $tab->tab_id ) ) {
        continue;
      }

      if ( $action === 'tab' . $i ) {
        ?>
        <div id="mepr_nav_tab_content_<?php echo $i; ?>">
          <?php echo do_shortcode( wpautop( stripslashes( $tab->content ) ) ); ?>
        </div>
        <?php
      }
    }
  }

  public function maybe_redirect_bp_tabs() {
    $is_enabled = (bool) get_option( 'mepr_account_nav_tabs_enabled', false );
    if ( ! $is_enabled ) {
      return;
    }
    $this->display_bp_nav_tab_content( true );
  }

  public function setup_bp_nav() {
    $is_enabled = (bool) get_option( 'mepr_account_nav_tabs_enabled', false );
    if ( ! $is_enabled ) {
      return;
    }
    $append_bp = ( function_exists( 'bp_is_active' ) && get_option( 'mepr_buddypress_enabled', 0 ) ) ? 'mp-subscriptions/' : '';

    if ( empty( $append_bp ) ) {
      return;
    }
    global $bp;

    $tabs = self::get_tabs();
    $slug = MeprHooks::apply_filters( 'mepr-bp-info-main-nav-slug', 'mp-membership' );
    $pos  = 100;

    if ( empty( $tabs ) ) {
      return;
    }

    // Get User Info
    $user = MeprUtils::get_currentuserinfo();

    foreach ( $tabs as $i => $tab ) {
      // Apply Tab Nav Rules
      if ( ! MeprUtils::is_mepr_admin() && MeprRule::is_locked_for_user( $user, $tab->tab_id ) ) {
        continue;
      }

      bp_core_new_subnav_item(
        array(
          'name'            => stripslashes( $tab->title ),
          'slug'            => 'mp-tab-' . $i,
          'parent_url'      => $bp->loggedin_user->domain . $slug . '/',
          'parent_slug'     => $slug,
          'screen_function' => array( $this, 'bp_display_screen' ),
          'position'        => $pos ++,
          'user_has_access' => bp_is_my_profile(),
          'site_admin_only' => false,
          'item_css_id'     => 'mepr-bp-tab-' . $i
        )
      );
    }
  }

  public function bp_display_screen() {
    add_action( 'bp_template_content', array( $this, 'display_bp_nav_tab_content' ) );
    bp_core_load_template( apply_filters( 'bp_core_load_template_plugin', 'members/single/plugins' ) );
  }

  public function display_bp_nav_tab_content( $redirect = false ) {
    $tabs = self::get_tabs();

    if ( empty( $tabs ) ) {
      return;
    }

    // Get User Info
    $user = MeprUtils::get_currentuserinfo();

    foreach ( $tabs as $i => $tab ) {
      // Apply Tab Nav Rules
      if ( ! MeprUtils::is_mepr_admin() && MeprRule::is_locked_for_user( $user, $tab->tab_id ) ) {
        continue;
      }

      if ( strpos($_SERVER['REQUEST_URI'], 'mp-tab-' . $i ) !== false ) {
        if ( ! $redirect ) {
          if ( $tab->type === 'content' ) { ?>
            <div id="mepr_nav_tab_content_<?php echo $i; ?>">
              <?php echo do_shortcode( wpautop( stripslashes( $tab->content ) ) ); ?>
            </div>
          <?php
          } else if ( $tab->type === 'url' ) { ?>
            <div id="mepr_nav_tab_content_<?php echo $i; ?>">
              <p><?php _e( 'Please wait while you are being redirected...', 'memberpress-account-nav-tabs' ); ?></p>
              <meta http-equiv="refresh" content="0; url=<?php echo stripslashes( $tab->url ); ?>" />
            </div>
          <?php
          }
        } else if ( $tab->type === 'url' ) {
          MeprUtils::wp_redirect(stripslashes( $tab->url ) );
          exit;
        }
      }
    }
  }


  /**
   * Add Account Nav Tab rule type
   *
   * @param array $all_types
   *
   * @return array
   */
  public function nav_tab_rule_type( $all_types ) {
    return array_merge(
      $all_types,
      array( 'account_nav_tab' => __( 'Account Nav Tab', 'memberpress', 'memberpress-account-nav-tabs' ) )
    );
  }


  /**
   * Get Account Nav Tab rule data
   *
   * @param array $contents
   * @param string $type
   *
   * @return array
   */
  public function nav_tab_contents_array( $contents, $type ) {
    if( $type == 'account_nav_tab' ) {
      $contents = self::find_all();
    }
    return $contents;
  }


  /**
   * Find all tabs titles
   *
   * @return array
   */
  public function find_all() {
    $all_tabs = self::get_tabs();
    $tabs = array();

    foreach( $all_tabs as $tab ) {
      $tabs[ $tab->tab_id ] = $tab->title;
    }

    return $tabs;
  }


   /**
   * Get Nav Tab by $id
   *
   * @param bool $content
   * @param string $type
   * @param int $id
   */
  public function nav_tab_content( $content, $type, $id ) {
    if( $type == 'account_nav_tab' ) {
      $tabs = self::get_tabs();

      foreach( $tabs as $tab ) {
        if( (int) $id === $tab->tab_id ) {
          $obj = new \stdClass();
          $obj->label = $tab->title;
          $obj->id = $tab->tab_id;
          if ( $tab->type === 'url' ) {
            $obj->desc = "Type: URL | {$tab->url}";
          } elseif ( $tab->type === 'content' ) {
            $obj->desc = "Type: Content | " . wp_trim_words( strip_tags( do_shortcode( $tab->content ) ), 10 );
          }
          return $obj;
        }
      }

    }

    return $content;
  }

  /**
   * Check whether there is a Nav Tab
   *
   * @param bool $has_content
   * @param string $type
   *
   * @return bool
   */
  public function has_nav_tab( $has_content, $type ) {
    if( $type == 'account_nav_tab' ) {
      return true;
    }

    return $has_content;
  }


  /**
   * Find Nav Tabs rule
   *
   * @param bool $content
   * @param string $type
   * @param string $search
   *
   * @return array
   */
  public function find_nav_tabs( $content, $type, $search ) {
    if( $type == 'account_nav_tab' ) {
      $all_tabs = self::get_tabs();
      $tabs = array();

      foreach( $all_tabs as $tab ) {
        if ( str_contains( strtolower( $tab->title ), strtolower( $search ) ) !== false ) {
          $obj = new \stdClass();
          $obj->id = $tab->tab_id;
          $obj->label = $tab->title;
          if ( $tab->type === 'url' ) {
            $obj->desc = "Type: URL | {$tab->url}";
          } elseif ( $tab->type === 'content' ) {
            $obj->desc = "Type: Content | " . wp_trim_words( strip_tags( do_shortcode( $tab->content ) ), 10 );
          }

          $tabs[] = $obj;
        }
      }

      return $tabs;

    }

    return $content;
  }


  /**
   * Protect/Exclude Nav Tabs based on rule
   *
   * @param array $rules
   * @param object $curr_rule
   * @param int $tab_id
   *
   * @return array
   */
  public function extend_rules( $rules, $curr_rule, $tab_id ) {
    if( $curr_rule->mepr_type == 'account_nav_tab' && $tab_id == $curr_rule->mepr_content ) {
      $rules[] = $curr_rule;
    }

    return $rules;
  }


  /**
   * Max ID checker
   *
   * @return int
   */
  public function max_id_checker() {
    $max_id = (int) get_option( 'mepr_account_nav_tabs_max_id', 0 );
    $tabs = self::get_tabs();
    if( $tabs ) {
      $last_tab_id = end($tabs)->tab_id;
    } else {
      $last_tab_id = 0;
    }

    $new_max_id = ( $max_id >= $last_tab_id ) ? $max_id : $last_tab_id;

    // Update Max ID
    update_option( 'mepr_account_nav_tabs_max_id', $new_max_id );

    // Return Max ID
    return $new_max_id;
  }


  /**
   * add Tabs IDs if not exist
   */
  public function add_tabs_ids() {
    // Get tabs to apply check if first item tab_id exists
    $old_tabs = self::get_tabs();

    if( $old_tabs && isset( $old_tabs[0]->tab_id ) ) {
      return false;
    }

    // The max ID in old tabs
    $max_id = self::max_id_checker();
    $tabs = array();

    foreach ( $old_tabs as $tab ) {
      $tabs[] = array(
        'tab_id'     => ( isset( $tab->tab_id ) ) ? (int) $tab->tab_id : (int) ++$max_id,
        'title'      => stripslashes( $tab->title ),
        'type'       => stripslashes( $tab->type ),
        'url'        => stripslashes( $tab->url ),
        'new_tab'    => ( isset( $tab->new_tab ) ) ? 1 : 0,
        'content'    => stripslashes( $tab->content )
      );
    }

    // Update tabs to add IDs
    update_option( 'mepr_account_nav_tabs', $tabs );

  }

}
