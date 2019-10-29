<?php
defined( 'ABSPATH' ) or die( 'Direct access denied!' ); // Security
/**
* One.com plugin update functionality
*/
if( ! defined( 'ONECOM_UPDATE_VERSION' ) ) {
	define( 'ONECOM_UPDATE_VERSION', '0.0.3' );
}
if( !defined( 'MIDDLEWARE_URL' ) ) {
	$api_version = 'v1.0';
    if( isset( $_SERVER[ 'ONECOM_WP_ADDONS_API' ] ) && $_SERVER[ 'ONECOM_WP_ADDONS_API' ] != '' ) {
        $ONECOM_WP_ADDONS_API = $_SERVER[ 'ONECOM_WP_ADDONS_API' ];
    } elseif( defined( 'ONECOM_WP_ADDONS_API' ) && ONECOM_WP_ADDONS_API != '' && ONECOM_WP_ADDONS_API != false ) {
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
	/*die(ONECOM_WP_CORE_VERSION);*/
}
if( !defined( 'ONECOM_PHP_VERSION' ) ) {
	define( 'ONECOM_PHP_VERSION' , phpversion() );
}
final class ONECOM_UPDATER {
    /**
    * Define variables which stores One.com themes and plugins
    **/
    private $onecom_installed_plugins, $onecom_installed_themes = array();

    public function __construct() {
        /**
        * Stores One.com themes and plugins based on author name and must match to One.com or one.com
        **/
        self::set_onecom_plugins();
        self::set_onecom_themes();

        /**
        * Filters to handle external URLS ( Need to be removed from production )
        **/
        add_filter('http_request_reject_unsafe_urls','__return_false');
        add_filter( 'http_request_host_is_external', '__return_true' );

        /**
        * WordPress filter to set third party plugin for an an u
        **/
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'onecom_check_for_plugin_update' ) );

        /**
        * WordPress filter to set third party theme for an an update
        **/
        add_filter( 'pre_set_site_transient_update_themes', array( $this, 'onecom_check_for_theme_update' ) );

        /**
        * WordPress filter to get changelog / info of a plugin
        **/
        add_filter( 'plugins_api', array( $this, 'one_plugins_api_callback' ) , 10, 3 );

        /******
        * Parked for future, check defination at end of file
        ******/
        // add_action( 'admin_enqueue_scripts', array( $this, 'onecom_updater_assets' ) );

        /******
        * As only we are providing free plugins we won't require custom messages on plugins.php
        ******/
        // self::onecom_upgrade_plugin_messages();
    }

    /**
    * Function to display changelog / info of a plugin
    **/
    public function one_plugins_api_callback( $false, $action, $args ) {
        if( $action == 'plugin_information' && ( isset( $_REQUEST[ 'section' ] ) && $_REQUEST[ 'section' ] == 'changelog' ) ) {
            $onecom_plugins = $this->onecom_installed_plugins;

            if( empty( $onecom_plugins ) ) {
                return false;
            }

            $onecom_plugins_keys = array_keys( $onecom_plugins );

            $onecom_plugins_keys = array_map( function( $value ) {
                return dirname( $value );
            }, $onecom_plugins_keys);

            if( in_array( $args->slug , $onecom_plugins_keys ) ) {
                $url = MIDDLEWARE_URL.'/plugins/'.$args->slug.'/info';
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
            }
        }
        return $false;
    }

    /**
    * Fetch all One.com plugins
    **/
    private function set_onecom_plugins() {


        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $plugins = get_plugins();
        /*echo "<pre>";die(var_dump($plugins ));echo "</pre>";*/

        foreach( $plugins as $file => $plugin ) :
            /*echo "<pre>";die(var_dump($plugin));echo "</pre>";*/
            if( 'one.com' === strtolower( $plugin[ 'Author' ] ) ) {
                $this->onecom_installed_plugins[ $file ] = $plugin;
            }
        endforeach;
    }

    /**
    * Function will return all One.com plugins
    **/
    /*public function get_onecom_plugins() {
        return self::onecom_installed_plugins;
    }*/

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
    * Function will return all One.com themes
    **/
    /*public function get_onecom_themes() {
        return self::onecom_installed_themes;
    }*/

    /**
    * Function get current version of plugin or theme
    **/
    private function get_current_version( $slug, $type, $file = null ) {

        if( 'plugins' === $type ) {
            $plugin_info = get_plugin_data( WP_PLUGIN_DIR.'/'.$file );
            return $plugin_info[ 'Version' ];
        } elseif( 'themes' === $type ) {
            $theme = wp_get_theme( $slug );
            return $theme->get( 'Version' );
        } else {
            return new WP_ERROR( 'message', 'Current version cannot be retrieve. Contact One.com support.' );
        }
    }

    /**
    * Function to get current & remote versions of all One.com themes
    **/
    private function get_onecom_themes_versions( $themes ) {
        $check_array = array();

        $check_array[ 'themes' ] = array();

        if( !empty( $themes ) ) :
            foreach ($themes as $template => $theme) :
                $temp = array();
                $temp[ 'slug' ] = $template;
                $temp[ 'installed_version' ] = $this->get_current_version( $template, 'themes' );
                $check_array[ 'themes' ][] = $temp;
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

            if( !empty( $remote_themes ) ) {
                foreach ($remote_themes as $key => $remote_theme) :
                    foreach( $themes as $template => $theme ) :
                        if( $template == $remote_theme->slug ) {
                            $themes[ $template ]->remote_version = $remote_theme->latest_version;
                            $themes[ $template ]->current_version = $this->get_current_version( $template, 'themes' );
                        }
                    endforeach;
                endforeach;
            }
        }

        return $themes;
    }

    /**
    * Function to get current & remote versions of all One.com plugins
    **/
    private function get_onecom_plugins_versions( $plugins ) {


        $check_array = array();

        $check_array[ 'plugins' ] = array();

        if( !empty( $plugins ) ) :
            foreach ( $plugins as $file => $plugin ) :
                $temp = array();
                $temp[ 'slug' ] = dirname( $file );
                $temp[ 'installed_version' ] = self::get_current_version( dirname( $file ), 'plugins', $file );
                $check_array[ 'plugins' ][] = $temp;
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

            if( !empty( $remote_plugins ) ) {
                foreach ($remote_plugins as $remote_plugin) :
                    foreach( $plugins as $file => $plugin ) :
                        $slug = dirname( $file );
                        if( $slug == $remote_plugin->slug ) {
                            $plugins[ $file ][ 'remote_version' ] = $remote_plugin->latest_version;
                            $plugins[ $file ][ 'current_version' ] = self::get_current_version( $slug, 'plugins', $file );
                        }
                    endforeach;
                endforeach;
            }
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

            if( isset( $plugin[ 'remote_version' ] ) ) {

                $remote_version = $plugin[ 'remote_version' ];
                $current_version = $plugin[ 'current_version' ];

                if ( version_compare( $current_version, $remote_version, '<' ) ) {
                    $plugin[ 'slug' ] = $slug;
                    $plugin[ 'new_version' ] = $remote_version;
                    $plugin[ 'plugin' ] = $file;
                    $plugin[ 'package' ] = $this->onecom_query_check ( MIDDLEWARE_URL.'/plugins/'.$slug.'/download' );
                    //$plugin['changelog'] = plugin_dir_url( __FILE__ ).'README.md';
                    //$plugin[ 'tested' ] = '4.7.2';

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
            /*$temp_theme = array();*/

            $remote_version = $theme->remote_version;
            $current_version = $theme->current_version;


            if( ! is_wp_error( $remote_version ) ) {
                if ( version_compare( $current_version, $remote_version, '<' ) ) {
                    $theme->slug = $template;
                    $theme->theme = $template;
                    $theme->new_version = $remote_version;
                    $theme->package = self::onecom_query_check ( MIDDLEWARE_URL.'/themes/'.$template.'/download' );
                    $theme->url = MIDDLEWARE_URL.'/themes/'.$template.'/info?section=changelog';
                    //$theme->url = get_template_directory_uri().'/changelog.html';
                    //$plugin[ 'tested' ] = '4.7.2';
                    $this->onecom_installed_themes[ $template ] = $theme;

                    $item = ( array ) $theme;
                    $checked_data->response[ $template ] = $item;
                } else {
                    unset( $checked_data->response[ $template ] ); // to remove response WP repo if any like gardener and express
                }
            }
        endforeach;

        /*echo '<pre>';
        print_r($checked_data);
        echo '</pre>';*/

        return $checked_data;
    }

    /**
    * Function to query update and append WP and PHP version to URL
    **/
    private function onecom_query_check( $url ) {
        $url = add_query_arg(
            array(
                'wp' => ONECOM_WP_CORE_VERSION,
                'php' => ONECOM_PHP_VERSION
            ), $url
        );
        return $url;
    }

    /**
    * Function to load assets requried for updates
    **/
    private function onecom_updater_assets( $hook ) {
        $hooks = array(
            'plugins.php',
            'update-core.php'
        );
        if( ! in_array( $hook, $hooks ) ) {
            return;
        }
        wp_register_script(
            $handle = 'onecom-updater',
            $src = plugin_dir_url( __FILE__ ).'update.js',
            $deps = array( 'jquery' ),
            $ver = ONECOM_UPDATE_VERSION
        );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'onecom-updater' );
    }

    /**
    * NOT IN USE - Function which will handle custom messages on plugins.php for third party plugins - parked for future
    **/
    private function onecom_upgrade_plugin_messages() {

        /**
        * As free plugins as of now we will skip checking for upgrade message.
        **/
        $plugin_info = get_site_transient('update_plugins');

        $response = $plugin_info->response;
        if( empty( $response ) ) {
            return;
        }
        $files = array_keys( $response );

        $items = $this->get_items();

        foreach ($items as $key => $item) :

            if( ! in_array( $item[ 'file' ], $files ) ) {
                continue;
            }

            add_action( 'in_plugin_update_message-' . $item[ 'file' ], array(
                $this,
                self::onecom_add_upgrade_message_link,
            ) );
        endforeach;

    }

    /**
    * NOT IN USE - Function for adding custom message or update link on plugins page, works with "in_plugin_update_message-" hook - parked for future
    **/
    private function onecom_add_upgrade_message_link( $plugin_data, $response ) {
        //echo '&nbsp;<a href="javascript:void(0)" class="onecom-update-plugin-link">'.__( 'Update now' ).'</a>';
    }


}

if( ! function_exists( 'onecom_is_plugin' ) ) {
	function onecom_is_plugin(){
	    return strpos( str_replace("\\", "/", plugin_dir_path( __FILE__ ) ) , str_replace("\\", "/", WP_PLUGIN_DIR) ) !== false;
	}
}

$instance = new ONECOM_UPDATER();