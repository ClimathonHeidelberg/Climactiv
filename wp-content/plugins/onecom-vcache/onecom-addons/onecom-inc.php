<?php
/* Copyright 2019: One.com */
include_once 'inc/logger.php';
include_once 'inc/ocver.php';

final class OCVCaching extends VCachingOC {
    const defaultTTL = 2592000; //1 month
    const defaultEnable = 'true';
    const defaultPrefix = 'varnish_caching_';
    const pluginName = 'onecom-vcache';
    const textDomain = 'vcaching';
    const transient = '__onecom_allowed_package';
    const getOCParam = 'purge_varnish_cache';
    const pluginVersion = '0.1.20';
    
    private $OCVer;
    private $logger;

    public $VCPATH;
    public $OCVCPATH;
    public $OCVCURI;
    public $state = 'false';

    private $messages = array();

    public function __construct() {
        $this->OCVCPATH = dirname( __FILE__ );
        $this->OCVCURI = plugins_url( null, __FILE__ );
        $this->VCPATH = dirname( $this->OCVCPATH );

        $this->logger = new Logger();
        $this->logger->setFileName( 'vcache' );

        add_action( 'init', array( $this, 'loadOCVer' ), 1 );
        add_action( 'admin_init', array( $this, 'runAdminSettings' ), 1 );
                
        add_action( 'admin_menu', array( $this, 'remove_parent_page' ), 100 );
        add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

        add_action( 'admin_init', array( $this, 'options_page_fields' ) );
        add_action( 'plugins_loaded', array( $this, 'filter_purge_settings' ), 1 );

        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_resources' ) );
	    add_action( 'admin_head', array( $this, 'onecom_vcache_icon_css' ));

	    // remove purge requests from Oclick demo importer
	    add_filter( 'vcaching_events', array( $this, 'vcaching_events_cb' ) );
	    //intercept the list of urls, replace multiple urls with a single generic url
	    add_filter( 'vcaching_purge_urls', array( $this, 'vcaching_purge_urls_cb' ) );

	    register_activation_hook( $this->VCPATH . DIRECTORY_SEPARATOR . 'vcaching.php', array( $this, 'onActivatePlugin' ) );
        register_deactivation_hook( $this->VCPATH . DIRECTORY_SEPARATOR . 'vcaching.php', array( $this, 'onDeactivatePlugin' ) );
    }

    /**
    * Function to load ocver
    *
    */
    public function loadOCVer() {
        $this->OCVer = new OCVer( $is_plugin = true, self::pluginName, $duration = 13 );
        $is_admin = is_admin();
        $isVer = $this->OCVer->isVer( self::pluginName, $is_admin );
        if( 'false' === $isVer ) {
            self::disableDefaultSettings();
        }
        else if('true' === $isVer) {
            self::setDefaultSettings();
            $this->state = 'true';
        }
    }

    /**
    * Function to run admin settings
    *
    */
    public function runAdminSettings() {
        if( 'false' !== $this->state ){
            return;
        }
        add_action( 'admin_bar_menu', array( $this, 'remove_toolbar_node' ), 999 );

        add_filter( 'post_row_actions', array( $this, 'remove_post_row_actions' ), 10, 2 );
        add_filter( 'page_row_actions', array( $this, 'remove_page_row_actions' ), 10, 2 );
    }

    /**
    * Function will execute after plugin activated
    *
    **/
    public function onActivatePlugin() {
        $this->logger->log( $message = self::pluginName.' plugin activated' );
        $this->logger->wpAPISendLog( self::pluginName, $action = 'activate', $message = self::pluginName.' plugin activated', self::pluginVersion );
        //self::setDefaultSettings();
        self::runChecklist();
    }

    /**
    * Function will execute after plugin deactivated
    *
    */
    public function onDeactivatePlugin() {
        $this->logger->log( $message = self::pluginName.' plugin deactivated' );
        $this->logger->wpAPISendLog( self::pluginName, $action = 'deactivate', $message = self::pluginName . ' plugin deactivated', self::pluginVersion );
        self::disableDefaultSettings( $onDeactivate = true );
        self::purgeAll();
    }

    /**
     * Function to make some checks to ensure best usage
     **/
    private function runChecklist() {

        // If not exist, then return
        if(!in_array('vcaching/vcaching.php', (array)get_option('active_plugins')))
            return true;

        $this->logger->wpAPISendLog( self::pluginName, $action = 'already_exists', $message = self::pluginName . 'DefaultWP Caching plugin already exists.', self::pluginVersion );
        add_action( 'admin_notices', array($this, 'duplicateWarning'));

        return false;
    }

    /**
     * Function to disable vcache promo/notice 
     *
     */
    private function disablePromoNotice() {
        $local_promo = get_site_option( 'onecom_local_promo' );
        if( ( isset( $local_promo[ 'xpromo' ] ) && $local_promo[ 'xpromo' ] == '18-jul-2018' ) ) {
            $local_promo[ 'show' ] = false;
            update_site_option( 'onecom_local_promo', $local_promo );
        }
    }

    /*
     * Show Admin notice
     */
    public function duplicateWarning(){

        $screen = get_current_screen();
        $warnScreens = array(
            'toplevel_page_onecom-vcache-plugin',
            'plugins',
            'options-general',
            'dashboard',
        );

        if(!in_array($screen->id, $warnScreens))
            return;

        $class = 'notice notice-warning is-dismissible';

        $dectLink = add_query_arg(
            array(
                'disable-old-varnish' => 1,
                '_wpnonce' => wp_create_nonce('disable-old-varnish')
            )
        );

        $dectLink  = wp_nonce_url($dectLink, 'plugin-deactivation');
        $message = __( 'To get the best out of One.com Performance Cache, kindly deactivate the existing "Varnish Caching" plugin.&nbsp;&nbsp;', self::textDomain );
        $message .= sprintf("<a href='%s' class='button'>%s</a>", ($dectLink), __('Deactivate'));
        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
    }

    /* Function to convert boolean to string
     *
     *
     */
    private function booleanCast( $value ) {
        if( ! is_string( $value ) ) {
            $value = ( 1 === $value || TRUE === $value ) ? 'true' : 'false';
        }
        if( '1' === $value ) {
            $value = 'true';
        }
        if( '0' === $value ) {
            $value = 'false';
        }
        return $value;
    }


    /**
    * Function to set default settings for One.com
    *
    **/
    private function setDefaultSettings() {
        // Enable by default
        $enable = $this->booleanCast( self::defaultEnable );
        $enabled = update_option( self::defaultPrefix . 'enable', $enable );
        if( ! $enabled ) {
            return;
        }

        $this->logger->log( $message = self::pluginName.' plugin featureEnabled' );
        $this->logger->wpAPISendLog( self::pluginName, $action = 'featureEnabled', $message = self::pluginName . ' feature enable', self::pluginVersion );

        // Update the cookie name
        if( ! get_option( self::defaultPrefix . 'cookie' ) ) {
            $name = sha1(md5(uniqid()));
            update_option( self::defaultPrefix . 'cookie', $name );
        }
         
        // Set default TTL
        $ttl = self::defaultTTL;
        if( ! get_option( self::defaultPrefix . 'ttl' ) && ! is_bool( get_option( self::defaultPrefix . 'ttl' ) ) && get_option( self::defaultPrefix . 'ttl' ) != 0 ) {
            update_option( self::defaultPrefix . 'ttl', $ttl );
        } elseif( ! get_option( self::defaultPrefix . 'ttl' ) && is_bool( get_option( self::defaultPrefix . 'ttl' ) ) ){
            update_option( self::defaultPrefix . 'ttl', $ttl );
        }
        if( ! get_option( self::defaultPrefix . 'homepage_ttl' ) && ! is_bool( get_option( self::defaultPrefix . 'homepage_ttl' ) ) && get_option( self::defaultPrefix . 'homepage_ttl' ) != 0 ) {
            update_option( self::defaultPrefix . 'homepage_ttl', $ttl );
        } elseif( ! get_option( self::defaultPrefix . 'homepage_ttl' ) && is_bool( get_option( self::defaultPrefix . 'homepage_ttl' ) ) ){
            update_option( self::defaultPrefix . 'homepage_ttl', $ttl );
        }

        // Set default varnish IP
        $ip = getHostByName( getHostName() );
        update_option( self::defaultPrefix . 'ips', $ip ); 

        if( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            update_option( self::defaultPrefix . 'debug', true );
        }

        // Deactivate the old varnish caching plugin on user's consent.
        if( (isset($_REQUEST['disable-old-varnish']) && $_REQUEST['disable-old-varnish'] == 1)){
            deactivate_plugins('/vcaching/vcaching.php');
            self::runAdminSettings();
            add_action( 'admin_bar_menu', array( $this, 'remove_toolbar_node' ), 999 );
        }

        // Check and notify if varnish plugin already active.
        if( in_array( 'vcaching/vcaching.php', (array)get_option('active_plugins'))){
            add_action( 'admin_notices', array( $this, 'duplicateWarning' ) );
        }
    }

    /**
    * Function to disable varnish plugin
    *
    **/
    private function disableDefaultSettings( $onDeactivate = false ) {
        // Disable by default
        $enable = $this->booleanCast( false );
        $disabled = update_option( self::defaultPrefix . 'enable', $enable );

        $action = ( TRUE === $onDeactivate ) ? 'disableManual' : 'featureDisabled';
        if( $disabled ) {
            $this->logger->log( $message = self::pluginName.' feature disabled '.$action );
            $this->logger->wpAPISendLog( self::pluginName, $action, $message = self::pluginName . ' feature disabled', self::pluginVersion );
            self::purgeAll();
        }
        
        delete_option( self::defaultPrefix . 'ttl' );
        delete_option( self::defaultPrefix . 'homepage_ttl' );
    }

    /**
    * Remove current menu item
    *
    */
    public function remove_parent_page() {
        remove_menu_page( 'vcaching-plugin' );
    }

    /**
    * Add menu item
    *
    */
    public function add_menu_item() {
        if (parent::check_if_purgeable()) {
            global $onecom_generic_menu_position;
            $position = ( function_exists('onecom_get_free_menu_position') && !empty($onecom_generic_menu_position) ) ? onecom_get_free_menu_position($onecom_generic_menu_position) : null;
            add_menu_page(__('Performance Cache', self::textDomain), __('Performance Cache', self::textDomain), 'manage_options', self::pluginName . '-plugin', array($this, 'settings_page'), 'dashicons-dashboard', $position );
        }
    }

    /**
    * Function to show settings page
    *
    */
    public function settings_page() {
        include_once $this->OCVCPATH . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'page-settings.php';
    }

    /**
    * Function to customize options fields
    *
    */
    public function options_page_fields() {
        add_settings_section(self::defaultPrefix . 'oc_options', null, null, self::defaultPrefix . 'oc_options');

        //add_settings_field(self::defaultPrefix . "enable", __("Enable" , self::textDomain), array($this, self::defaultPrefix . "enable_callback"), self::defaultPrefix . 'oc_options', self::defaultPrefix . 'oc_options', array( 'class' => 'hide-th' ));
        add_settings_field(self::defaultPrefix . "ttl", __("Cache TTL", self::textDomain) . '<span class="tooltip"><span class="dashicons dashicons-editor-help"></span><span>'.__( 'The time that website data is stored in the Varnish cache. After the TTL expires the data will be updated, 0 means no caching.', self::textDomain ).'</span></span>', array($this, self::defaultPrefix . "ttl_callback"), self::defaultPrefix . 'oc_options', self::defaultPrefix . 'oc_options');

        if(isset($_POST['option_page']) && $_POST['option_page'] == self::defaultPrefix . 'oc_options') {
            register_setting(self::defaultPrefix . 'oc_options', self::defaultPrefix . "enable");
            register_setting( self::defaultPrefix . 'oc_options', self::defaultPrefix . "ttl");
           
            $ttl = $_POST[ self::defaultPrefix . 'ttl' ];
            $is_update = update_option( self::defaultPrefix . "homepage_ttl", $ttl ); //overriding homepage TTL
        }

        self::disablePromoNotice();
    }

    /**
    * Function enqueue resources
    *
    */
    public function enqueue_resources( $hook ) {
        if( 'toplevel_page_onecom-vcache-plugin' !== $hook ) {
            return;
        }
        wp_register_style( 
            $handle = self::pluginName, 
            $src = $this->OCVCURI.'/assets/css/style.css', 
            $deps = null, 
            $ver = null, 
            $media = 'all'
        );
        wp_enqueue_style( self::pluginName );

        /* Google fonts */
        if( ! wp_style_is( 'onecom-wp-google-fonts', 'registered' ) ) {
            wp_register_style( 
                $handle = 'onecom-wp-google-fonts', 
                $src = '//fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800', 
                $deps = null, 
                $ver = null, 
                $media = 'all'
            );
        }
        wp_enqueue_style( 'onecom-wp-google-fonts' );
    }

    /* Function to enqueue style tag in admin head
     * */
    function onecom_vcache_icon_css(){
        echo "<style>.ab-top-menu #wp-admin-bar-purge-all-varnish-cache .ab-icon:before,#wpadminbar>#wp-toolbar>#wp-admin-bar-root-default>#wp-admin-bar-onecom-wp .ab-item:before{top: 2px;}a.current.menu-top.toplevel_page_onecom-vcache-plugin.menu-top-last{word-spacing: 10px;}</style>";
        return;
    }

    /**
    * Function to create enable field
    *
    */
    public function varnish_caching_enable_callback() {
        ?>
            <input type="checkbox" id="varnish_caching_enable" name="varnish_caching_enable" value="1" <?php checked( 'true', get_option( self::defaultPrefix . 'enable' ), true ); ?> />
            <label for="varnish_caching_enable"><?=__('Enable Performance Cache', self::textDomain)?></label>
        <?php
    }

    /**
    * Function to create TTL field
    *
    */
    public function varnish_caching_ttl_callback() {
        ?>
            <input type="number" name="varnish_caching_ttl" id="varnish_caching_ttl" value="<?php echo get_option(self::defaultPrefix . 'ttl'); ?>" /> 
            <p class="description"><?=__('Time to live in seconds in Varnish cache', self::textDomain)?></p>
        <?php
    }

    /**
    * Function to purge all
    *
    */
    private function purgeAll() {
        $pregex = '.*';
        $purgemethod = 'regex';
        $path = '/';
        $schema = 'http://';

        $ip = get_option( self::defaultPrefix . 'ips' );

        $purgeme = $schema . $ip . $path . $pregex;

        $headers = array(
            'host' => $_SERVER['SERVER_NAME'], 
            'X-VC-Purge-Method' => $purgemethod, 
            'X-VC-Purge-Host' => $_SERVER['SERVER_NAME']
        );
        $response = wp_remote_request( 
            $purgeme, 
            array(
                'method' => 'PURGE', 
                'headers' => $headers, 
                "sslverify" => false
            )
        );
        if ($response instanceof WP_Error) {
            error_log("Cannot purge: ".$purgeme);
        } else {
            //error_log("Purged: ".json_encode($response));
        }
    }

    /**
    * Function to change purge settings
    *
    */
    public function filter_purge_settings() {
        add_filter( 'ocvc_purge_notices', array( $this, 'ocvc_purge_notices_callback' ), 10, 2 );
        add_filter( 'ocvc_purge_url', array( $this, 'ocvc_purge_url_callback' ), 1, 3 );
        add_filter( 'ocvc_purge_headers', array( $this, 'ocvc_purge_headers_callback' ), 1, 2 );
        add_filter( 'ocvc_permalink_notice', array( $this, 'ocvc_permalink_notice_callback' ) );
        add_filter( 'vcaching_purge_urls', array( $this, 'vcaching_purge_urls_callback' ), 10, 2 );

        add_action( 'admin_notices', array( $this, 'oc_vc_notice' ) );
    }

    /**
    * Function to filter the purge request response
    *
    * @param object $response //request response object
    * @param string $url // url trying to purge
    */
    public function ocvc_purge_notices_callback( $response, $url ) {

        $response = wp_remote_retrieve_body( $response );
        
        $find = array(
            '404 Key not found' => sprintf( __( 'It seems that %s is already purged. There is no resource in the cache to purge.', self::textDomain ), $url ),
            'Error 200 Purged' => sprintf( __( '%s is purged successfully.', self::textDomain ), $url ),
        );

        foreach ( $find as $key => $message ) {
            if( strpos( $response, $key ) !== false ) {
                array_push( $this->messages, $message );
            }   
        }
        //[28-May-2019] Removing $_SESSION usage
        // $_SESSION['ocvcaching_purge_note'] = $this->messages;

    }

    /**
    * Function to add notice
    *
    */
    public function oc_vc_notice() {
        if( empty( $this->messages ) && empty( $_SESSION['ocvcaching_purge_note'] ) ) return;
        ?>
            <div class="notice notice-warning">
                <ul>
                    <?php
                        if( ! empty( $this->messages ) ) {
                            foreach ( $this->messages as $key => $message ) {
                                if( $key > 0 )
                                    break;
                                ?>
                                    <li><?php echo $message; ?></li>
                                <?php 
                            }
                        }
                        elseif( ! empty( $_SESSION['ocvcaching_purge_note'] ) ) {
                            foreach ( $_SESSION['ocvcaching_purge_note'] as $key => $message ) {
                                if( $key > 0 )
                                    break;
                                ?>
                                    <li><?php echo $message; ?></li>
                                <?php 
                            }
                            //[28-May-2019] Removing $_SESSION usage
                            //$_SESSION['ocvcaching_purge_note'] = array();
                        }
                    ?>
                </ul>
            </div>
        <?php
    }

    /**
    * Function to change purge URL
    *
    * @param string $url //URL to be purge
    * @param string $path //Path of URL
    * @param string $prefex //Regex if any
    * @return string $purgeme //URL to be purge
    */
    public function ocvc_purge_url_callback( $url, $path, $pregex ) {
        $p = parse_url($url);

        $scheme = (isset($p['scheme']) ? $p['scheme'] : '');
        $host = (isset($p['host']) ? $p['host'] : '');
        $purgeme = $scheme . '://' . $host . $path . $pregex;
        
        return $purgeme;
    }

    /**
    * Function to change purge request headers
    *
    * @param string $url //URL to be purge
    * @param array $headers //Headers for the request
    * @return array $headers //New headers
    */
    public function ocvc_purge_headers_callback( $url, $headers ) {
        $p = parse_url($url);
        if (isset($p['query']) && ($p['query'] == 'vc-regex')) {
            $purgemethod = 'regex';
        } else {
            $purgemethod = 'exact';
        }
        $headers[ 'X-VC-Purge-Host' ] = $_SERVER[ 'SERVER_NAME' ];
        $headers[ 'host' ] = $_SERVER[ 'SERVER_NAME' ];
        $headers[ 'X-VC-Purge-Method' ] = $purgemethod;
        return $headers;
    }

    /**
    * Function to change permalink message
    *
    */
    public function ocvc_permalink_notice_callback( $message ) {
        $message = __( 'A custom URL or permalink structure is required for the Performance Cache plugin to work correctly. Please go to the <a href="options-permalink.php">Permalinks Options Page</a> to configure them.', self::textDomain );
        return '<div class="notice notice-warning"><p>'.$message.'</p></div>';
    }

    /**
    * Function to notify customer about auto deactivate plugin 
    * [Not in use]
    */
    public function notifyCustomer() {
        $toEmail = get_option('admin_email');

        $message_id = time() .'-' . md5($toEmail) . '@' . $_SERVER['SERVER_NAME'];
        $header = array(
            "MIME-Version: 1.0",
            "Content-type: text/html; charset: utf8",
            "X-Mailer: PHP/" . PHP_VERSION,
            "X-Priority: 1 (Highest)",
            "Importance: High",
            "Date: ".date("r"),
            "Message-Id: <".$message_id.">"
        );

        $message  = __( "Hello, We've deactivated the One.com Varnish plugin from ".home_url(), self::textDomain );
        $subject = __( "Deactivated the One.com Varnish plugin", self::textDomain );

        $is_mailed = wp_mail( $toEmail, $subject, $message, implode( "\r\n", $header ) );
        if( is_wp_error( $is_mailed ) )  {
            error_log("ERROR mail: ".$is_mailed->get_error_message());
        } else {
            error_log("Mail sent");
        }
    }

    /**
    * Function to deactivate self
    * [Not in use]
    */
    public function deactivateSelf() {
        deactivate_plugins( dirname( plugin_basename( $this->OCVCPATH ) ) . DIRECTORY_SEPARATOR . 'vcaching.php' );
        //header('Location: '.$_SERVER['REQUEST_URI']);
    }

    /**
    * Function to to remove menu item from admin menu bar
    *
    */
    public function remove_toolbar_node( $wp_admin_bar ) {
        // replace 'updraft_admin_node' with your node id
        $wp_admin_bar->remove_node('purge-all-varnish-cache');
    }

    /**
    * Function to to remove purge cache from post
    *
    */
    public function remove_post_row_actions($actions, $post) {
        if( isset( $actions[ 'vcaching_purge_post' ] ) ) {
            unset( $actions[ 'vcaching_purge_post' ] );
        }
        return $actions;
    }

    /**
    * Function to to remove purge cache from page
    *
    */
    public function remove_page_row_actions($actions, $post) {
        if( isset( $actions[ 'vcaching_purge_page' ] ) ) {
            unset( $actions[ 'vcaching_purge_page' ] );
        }
        return $actions;
    }

    /**
    * Function to set purge single post/page URL
    *
    * @param array $array // array of urls
    * @param number $post_id //POST ID
    */
    public function vcaching_purge_urls_callback( $array, $post_id ) {
        $url = get_permalink( $post_id );
        array_unshift( $array, $url );
        return $array;
    }

	/**
	 * Function vcaching_events_cb
	 * Callback function for vcaching_events WP filter
	 * This function checks if the registered events are to be returned, judging from request payload.
	 * e.g. the events are nulled for request actions like "heartbeat" and  "ocdi_import_demo_data"
	 * @param $events, an array of events on which caching is hooked.
	 * @return array
	 */
	function vcaching_events_cb( $events ) {

		$no_post_action = ! isset($_REQUEST['action']);
		$action_not_watched = isset( $_REQUEST['action'] ) && ($_REQUEST['action'] === 'ocdi_import_demo_data' || $_REQUEST['action'] === 'heartbeat');

		if ($no_post_action || $action_not_watched) {
			return [];
		} else {
			return $events;
		}
	}

	/**
	 * Function vcaching_purge_urls_cb
	 * Callback function for vcaching_purge_urls WP filters
	 * This function removes all the urls that are to be purged and returns single url that purges entire cache.
	 * @param $urls, an array of urls that were originally to be purged.
	 * @return array
	 */
	function vcaching_purge_urls_cb( $urls ) {
		$site_url = trailingslashit( get_site_url() );
		$purgeUrl = $site_url . '.*';
		$urls     = array( $purgeUrl );
		return $urls;
	}
}
$OCVCaching = new OCVCaching();