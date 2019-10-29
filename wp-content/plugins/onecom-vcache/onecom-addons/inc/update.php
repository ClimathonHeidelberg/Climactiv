<?php
defined( 'ABSPATH' ) or die( 'Direct access denied!' ); // Security
/**
* One.com plugin update functionality
*/
if( ! defined( 'ONECOM_UPDATE_VERSION' ) ) {
	define( 'ONECOM_UPDATE_VERSION', '0.0.5' );
}

if(!defined('OC_WP_API')){
    define( 'OC_WP_API', 'ONECOM_WP_ADDONS_API' );
}

if(!defined('OC_PLUGINS')){
    define( 'OC_PLUGINS', 'plugins' );
}

if(!defined('OC_THEMES')){
    define( 'OC_THEMES', 'themes' );
}

if(!defined('OC_REM_VER')){
    define( 'OC_REM_VER', 'remote_version' );
}

if( !defined( 'MIDDLEWARE_URL' ) ) {
	$api_version = 'v1.0';
    if( isset( $_SERVER[ OC_WP_API ] ) && $_SERVER[ OC_WP_API ] != '' ) {
        $ONECOM_WP_ADDONS_API = $_SERVER[ OC_WP_API ];
    } elseif( defined( OC_WP_API ) && ONECOM_WP_ADDONS_API != '' && ONECOM_WP_ADDONS_API ) {
        $ONECOM_WP_ADDONS_API = ONECOM_WP_ADDONS_API;
    } else {
        $ONECOM_WP_ADDONS_API = 'http://wpapi.one.com/';
    }
    $ONECOM_WP_ADDONS_API = rtrim( $ONECOM_WP_ADDONS_API, '/' );
	define( 'MIDDLEWARE_URL', $ONECOM_WP_ADDONS_API.'/api/'.$api_version );
}
if( !defined( 'ONECOM_WP_CORE_VERSION' ) ) {
	global $wp_version;
	define( 'ONECOM_WP_CORE_VERSION' , $wp_version );
}
if( !defined( 'ONECOM_PHP_VERSION' ) ) {
	define( 'ONECOM_PHP_VERSION' , phpversion() );
}
final class ONECOMUPDATER {
    /**
    * Define variables which stores One.com themes and plugins
    **/
    private $onecom_installed_plugins, $onecom_installed_themes = array();

    public function __construct() {
        /**
        * Stores One.com themes and plugins based on author name and must match to One.com or one.com
        **/
        add_action('wp_loaded', [$this, 'set_onecom_themes_plugins']);

        /**
        * Filters to handle external URLS ( Need to be removed from production )
        **/
        add_filter('http_request_reject_unsafe_urls','__return_false');
        add_filter( 'http_request_host_is_external', '__return_true' );

        /**
        * WordPress filter to set third party plugin for an update
        **/
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'onecom_check_for_plugin_update' ) );

        /**
        * WordPress filter to set third party theme for an update
        **/
        add_filter( 'pre_set_site_transient_update_themes', array( $this, 'onecom_check_for_theme_update' ) );

        /**
        * WordPress filter to get changelog / info of a plugin
        **/
        add_filter( 'plugins_api', array( $this, 'one_plugins_api_callback' ) , 10, 3 );
    }
    
    /**
     * Stores One.com themes and plugins based on author name and must match to One.com or one.com
     **/
    public function set_onecom_themes_plugins()
    {
        self::set_onecom_plugins();
        self::set_onecom_themes();
    }

    /**
     * Check if plugin is a one.com plugin
     */
    public function one_check_if_oc_plugin($slug, $onecom_plugins_keys){
        if( in_array( $slug , $onecom_plugins_keys ) ) {
            $url = MIDDLEWARE_URL.'/plugins/'.$slug.'/info';
            $response = wp_remote_get( $url );
            if( ! is_wp_error( $response ) ) {
                $result = wp_remote_retrieve_body( $response );
                if ( ! is_wp_error( $result ) ) {
                    $json = json_decode( $result );
                    if( json_last_error() === 0 ) {
                        echo $json->error;
                    } else {
                        echo $result;
                    }
                    die();
                }
            }
            echo 'Error when reading changelog';
            die();
        }else{
            return false;
        }
    }

    /**
    * Function to display changelog / info of a plugin
    **/
    public function one_plugins_api_callback( $false, $action, $args ) {
        
	    if( $action == 'plugin_information' && (( isset( $_REQUEST[ 'section' ] ) && $_REQUEST[ 'section' ] == 'changelog' ) || ( isset( $_REQUEST[ 'tab' ] ) && $_REQUEST[ 'tab' ] == 'plugin-information' )) ) {
            $onecom_plugins = $this->onecom_installed_plugins;

            if( empty( $onecom_plugins ) ) {
                return false;
            }

            // get plugin file names
            $onecom_plugins_keys = array_keys( $onecom_plugins );

            // get plugin file directory names
            $onecom_plugins_keys = array_map( function( $value ) {
                return dirname( $value );
            }, $onecom_plugins_keys);

            // sanitizing plugin directory names
            $onecom_plugins_keys = array_map( function( $value ) {
                return str_replace("one.com-wp-plugin-", "", $value);
            }, $onecom_plugins_keys);
            
            // check if clicked plugin is a one.com plugin
            return $this->one_check_if_oc_plugin($args->slug , $onecom_plugins_keys);
            
        }
        return $false;
    }


    /**
    * Add "View Details" link for all One.com plugins, if not exist
    **/
    public function onecom_plugin_row_meta( $links, $file ) {
        
        // skip all non-one.com plugin entries
        if(!array_key_exists($file, $this->onecom_installed_plugins)){
            return $links;
        }

        $plugin = str_replace('one.com-wp-plugin-', '', dirname($file));

        // check if View details link already present, Exit if yes.
        foreach ($links as $link){
            if(strpos($link, 'plugin-install.php?tab=plugin-information') !== false){
                return $links;
            }
        }
        // add new link - "View Details"
        $new_links = array(
            'view-details' => sprintf('<a class="thickbox open-plugin-details-modal" href="%s?tab=plugin-information&plugin=%s&section=changelog&TB_iframe=true&width=600&height=800" target="_blank">%s</a>',admin_url('plugin-install.php'), $plugin, __('View details'))
        );
        
        // club the new link with existing links
        return array_merge( $links, $new_links );
    }


    /**
    * Fetch all One.com plugins
    **/
    private function set_onecom_plugins() {


        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $plugins = get_plugins();

        foreach( $plugins as $file => $plugin ) :
            if( 'one.com' === strtolower( $plugin[ 'Author' ] ) ) {
                $this->onecom_installed_plugins[ $file ] = $plugin;
            }
        endforeach;

        /* Add "View Details" link for all One.com plugins, if not exist */
        add_filter( 'plugin_row_meta', array($this, 'onecom_plugin_row_meta'), 10, 2 );
    }

    /**
    * Fetch all One.com themes
    **/
    private function set_onecom_themes() {
        $themes = wp_get_themes();
        foreach( $themes as $template => $theme ) :
            if( 'one.com' === strtolower( $theme->display( 'Author', FALSE ) ) ) {
                $this->onecom_installed_themes[ $template ] = $theme;
            }
        endforeach;
    }

    /**
    * Function get current version of plugin or theme
    **/
    private function get_current_version( $slug, $type, $file = null ) {

        if( OC_PLUGINS === $type ) {
            $plugin_info = get_plugin_data( WP_PLUGIN_DIR.'/'.$file );
            return $plugin_info[ 'Version' ];
        } elseif( OC_THEMES === $type ) {
            $theme = wp_get_theme( $slug );
            return $theme->get( 'Version' );
        } else {
            return new WP_ERROR( 'message', 'Current version cannot be retrieve. Contact One.com support.' );
        }
    }

    /**
     * Prepare one.com themes
     */
    public function oc_prepare_remote_themes($remote_themes, $themes){
        if( !empty( $remote_themes ) ) {
            foreach ($remote_themes as $remote_theme) :
                foreach( $themes as $template => $theme ) :
                    if( $template == $remote_theme->slug ) {
                        $themes[ $template ]->remote_version = $remote_theme->latest_version;
                        $themes[ $template ]->current_version = $this->get_current_version( $template, OC_THEMES );
                    }
                endforeach;
            endforeach;

            return $themes;
        }
    }

    /**
    * Function to get current & remote versions of all One.com themes
    **/
    private function get_onecom_themes_versions( $themes ) {
        $check_array = array();

        $check_array[ OC_THEMES ] = array();

        if( !empty( $themes ) ) :
            foreach ($themes as $template => $theme) :
                $temp = array();
                $temp[ 'slug' ] = $template;
                $temp[ 'installed_version' ] = $this->get_current_version( $template, OC_THEMES );
                $check_array[ OC_THEMES ][] = $temp;
            endforeach;
        endif;

        $check_array[ 'php_version' ] = ONECOM_PHP_VERSION;
        $check_array[ 'wp_version' ] = ONECOM_WP_CORE_VERSION;

        $url = MIDDLEWARE_URL.'/themes/update';

        global $wp_version;
        $args = array(
            'timeout'     => 10,
            'httpversion' => '1.0',
            'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
            'body'        => json_encode( $check_array ),
            'compress'    => false,
            'decompress'  => true,
            'sslverify'   => true,
            'stream'      => false,
        );
        $response = wp_remote_post( $url, $args );
        $body = wp_remote_retrieve_body( $response );
        $result = json_decode( $body );

        if( $result->success ) {
            $remote_themes = $result->data;
            return $this->oc_prepare_remote_themes($remote_themes, $themes);
        }

        return $themes;
    }

    public function oc_prepare_remote_plugins($remote_plugins, $plugins){
        if( !empty( $remote_plugins ) ) {
            foreach ($remote_plugins as $remote_plugin) :
                foreach( $plugins as $file => $plugin ) :
                    $slug = dirname( $file );
                    if( $slug == $remote_plugin->slug ) {
                        $plugins[ $file ][ OC_REM_VER ] = $remote_plugin->latest_version;
                        $plugins[ $file ][ 'current_version' ] = self::get_current_version( $slug, OC_PLUGINS, $file );
                    }
                endforeach;
            endforeach;

            return $plugins;
        }
    }

    /**
    * Function to get current & remote versions of all One.com plugins
    **/
    private function get_onecom_plugins_versions( $plugins ) {


        $check_array = array();

        $check_array[ OC_PLUGINS ] = array();

        if( !empty( $plugins ) ) :
            foreach ( $plugins as $file => $plugin ) :
                $temp = array();
                $temp[ 'slug' ] = dirname( $file );
                $temp[ 'installed_version' ] = self::get_current_version( dirname( $file ), OC_PLUGINS, $file );
                $check_array[ OC_PLUGINS ][] = $temp;
            endforeach;
        endif;

        $check_array[ 'php_version' ] = ONECOM_PHP_VERSION;
        $check_array[ 'wp_version' ] = ONECOM_WP_CORE_VERSION;

        $url = MIDDLEWARE_URL.'/plugins/update';

        global $wp_version;
        $args = array(
            'timeout'     => 10,
            'httpversion' => '1.0',
            'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
            'body'        => json_encode( $check_array ),
            'compress'    => false,
            'decompress'  => true,
            'sslverify'   => true,
            'stream'      => false,
        );
        $response = wp_remote_post( $url, $args );

        $body = wp_remote_retrieve_body( $response );
        $result = json_decode( $body );

        if( isset( $result->success ) && $result->success ) {
            $remote_plugins = $result->data;

            return $this->oc_prepare_remote_plugins($remote_plugins, $plugins);
        }

        return $plugins;
    }

    /**
    * Function check update for One.com plugins
    **/
    public function onecom_check_for_plugin_update( $checked_data  ) {

        if ( empty( $checked_data->checked ) ) {
            return $checked_data;
        }

        $installed_plugins = $this->onecom_installed_plugins;

        if( empty( $installed_plugins ) ) {
            return $checked_data;
        }

        $installed_plugins = self::get_onecom_plugins_versions( $installed_plugins );

        foreach ($installed_plugins as $file => $plugin) :
            $slug = dirname( $file );

            if( isset( $plugin[ OC_REM_VER ] ) ) {

                $remote_version = $plugin[ OC_REM_VER ];
                $current_version = $plugin[ 'current_version' ];

                if ( version_compare( $current_version, $remote_version, '<' ) ) {
                    $plugin[ 'slug' ] = $slug;
                    $plugin[ 'new_version' ] = $remote_version;
                    $plugin[ 'plugin' ] = $file;
                    $plugin[ 'package' ] = $this->onecom_query_check ( MIDDLEWARE_URL.'/plugins/'.$slug.'/download' );

                    $this->onecom_installed_plugins[ $file ] = $plugin;

                    $item = ( object ) $plugin;
                    $checked_data->response[ $file ] = $item;
                } else {
                    unset( $checked_data->response[ $file ] ); // to remove response WP repo if any like gardener and express
                }
            }
        endforeach;

        return $checked_data;
    }

    /**
    * Function to check update for One.com themes
    **/
    public function onecom_check_for_theme_update( $checked_data  ) {

        if ( empty( $checked_data->checked ) ) {
            return $checked_data;
        }

        $installed_themes = $this->onecom_installed_themes;

        if( empty( $installed_themes ) ) {
            return $checked_data;
        }

        $installed_themes = self::get_onecom_themes_versions( $installed_themes );

        foreach ($installed_themes as $template => $theme) :

            $remote_version = $theme->remote_version;
            $current_version = $theme->current_version;


            if( ! is_wp_error( $remote_version ) ) {
                if ( version_compare( $current_version, $remote_version, '<' ) ) {
                    $theme->slug = $template;
                    $theme->theme = $template;
                    $theme->new_version = $remote_version;
                    $theme->package = self::onecom_query_check ( MIDDLEWARE_URL.'/themes/'.$template.'/download' );
                    $theme->url = MIDDLEWARE_URL.'/themes/'.$template.'/info?section=changelog';
                    $this->onecom_installed_themes[ $template ] = $theme;
                    $item = ( array ) $theme;
                    $checked_data->response[ $template ] = $item;
                } else {
                    unset( $checked_data->response[ $template ] ); // to remove response WP repo if any like gardener and express
                }
            }
        endforeach;
        return $checked_data;
    }

    /**
    * Function to query update and append WP and PHP version to URL
    **/
    private function onecom_query_check( $url ) {
        return add_query_arg(
            array(
                'wp' => ONECOM_WP_CORE_VERSION,
                'php' => ONECOM_PHP_VERSION
            ), $url
        );
    }

}

if( ! function_exists( 'onecom_is_plugin' ) ) {
	function onecom_is_plugin(){
	    return strpos( str_replace("\\", "/", plugin_dir_path( __FILE__ ) ) , str_replace("\\", "/", WP_PLUGIN_DIR) ) !== false;
	}
}

$instance = new ONECOMUPDATER();