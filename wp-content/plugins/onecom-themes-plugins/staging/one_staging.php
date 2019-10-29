<?php
/**
 * @package OneStaging
 * @category Core
 * @owner One.com
 * @author Robin Chalia <robinc@one.com>
 */
namespace OneStaging;

defined( "WPINC" ) or die(); // No Direct Access

// Autoloader
require_once __DIR__ . DIRECTORY_SEPARATOR . "Includes" . DIRECTORY_SEPARATOR . "Autoloader.php";

use OneStaging\Core\Cache;
use OneStaging\Core\Loader;
use OneStaging\Core\Logger;
use OneStaging\Core\Settings;
use OneStaging\Includes\Autoloader;
use OneStaging\Core\Administrator;

class OneStaging {

	private $services;

	private static $instance;

	private static $settings;

	private function __construct() {
		$file = ONECOM_WP_PATH . "staging" . DIRECTORY_SEPARATOR . "one_staging" . ".php";

		$this->slug = plugin_basename( dirname( dirname( ( __FILE__ ) ) ) );

		$this->registerMain();
		$this->registerNamespaces();
		$this->loadWPDB();
		$this->loadDependencies();
		$this->defineHooks();
	}

	/**
	 * Get table prefix of the current site
	 * @return string
	 */
	public static function getTablePrefix(){
		$wpDB = OneStaging::getInstance()->get("wpdb");
		return $wpDB->prefix;
	}

	public static function getContentDir() {
		$wp_upload_dir = wp_upload_dir();
		$path = $wp_upload_dir['basedir'] . '/one_staging';
		wp_mkdir_p( $path );
		return apply_filters( 'wpstg_get_upload_dir', $path . DIRECTORY_SEPARATOR );
	}

	/**
	 * Load WP DB
	 */
	public function loadWPDB() {
		global $wpdb;
		if(isset($wpdb))
			$this->set("wpdb", $wpdb);
		return $this;
	}

	/**
	 * Register used namespaces
	 */
	private function registerNamespaces() {
		$autoloader = new Autoloader();
		$this->set( "autoloader", $autoloader );

		// Autoloader
		$autoloader->registerNamespaces( array(
			"OneStaging" => array(
				$this->getSlug() . 'Includes' . DIRECTORY_SEPARATOR,
				$this->getSlug() . 'Includes' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR,
				$this->getSlug() . 'Includes' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Jobs' .DIRECTORY_SEPARATOR,
			)
		) );

		// Register namespaces
		$autoloader->register();
	}


	public function registerMain(){
		// Slug of the plugin
		$this->slug = plugin_basename( dirname( dirname( ( __FILE__ ) ) ) );

		// absolute path to the main plugin dir
		$this->pluginPath = plugin_dir_path( dirname(( __FILE__ ) ) ).'staging'.DIRECTORY_SEPARATOR;

		// absolute URL to the main plugin dir
		$this->pluginURL = plugin_dir_url( __FILE__ );

		// URL to Core folder
		$this->url = plugin_dir_url( dirname( __FILE__ ) ).'staging/';
	}


	/**
	 * Define Hooks
	 */
	public function defineHooks() {
		$loader = $this->get( "loader" );
		$loader->addAction( "admin_enqueue_scripts", $this, "enqueueElements", 100 );
		$loader->addAction( "wp_enqueue_scripts", $this, "enqueueElements", 100 );
	}


	/**
	 * Scripts and Styles
	 * @param string $hook
	 */
	public function enqueueElements( $hook ) {

		$res_ext = ( SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : '.min';
		$res_dir = ( SCRIPT_DEBUG || SCRIPT_DEBUG == 'true') ? '' : 'min-';

		// Load this css file on frontend and backend on all pages if current site is a staging site
		if( $this->get('settings')->_isStaging === true ) {
			wp_enqueue_style( "onecom-staging-adminbar", ONECOM_WP_URL."assets/css/onecom-admin-staging.css", $this->getVersion() );
		}

		$availablePages = array(
			"onecom-wp-staging",
			"one-com_page_onecom-wp-staging",
		);

		// Load these css and js files only on wp staging admin pages
		if( !in_array( $hook, $availablePages ) || !is_admin() ) {
			return;
		}

		// Load admin js files
		wp_enqueue_script(
			"onecom-stg-admin-script", ONECOM_WP_URL."assets/".$res_dir."js/onecom_staging".$res_ext.".js", array("jquery"), $this->getVersion(), true
		);

		wp_localize_script( "onecom-stg-admin-script", "wpstg", array(
				"nonce" => wp_create_nonce( "wpstg_ajax_nonce" ),
				"stgID" => self::get('settings')->getStagingDir(),
				"stgPrefix" => self::get('settings')->getStagingPrefix(),
				"status" =>__( "Status", "Current request status", "onecom-wp" ),
				"response" =>__( "Response", "The message the server responded with", "onecom-wp" ),
				"copy_msg" =>__("Copied successfully.", "onecom-wp" ),
				"error_msg" =>__("Something went wrong; please try again.", "onecom-wp" ),
				"cpuLoad" => $this->getCPULoadSetting(),
				"settings" => ( object ) array(), // TODO add settings?
				"tblprefix" => self::getTablePrefix(),
				"msgPrep" =>__("Preparing...", "onecom-wp"),
				"msgNewStg" =>__("Start copying...", "onecom-wp"),
				"msgCopyDB" =>__("Copying database...", "onecom-wp"),
				"msgPrepDirs" =>__("Preparing directories...", "onecom-wp"),
				"msgCopyFiles" =>__("Copying files...", "onecom-wp"),
				"msgUpdateDB" =>__("Updating database...", "onecom-wp"),
				"msgFinalize" =>__("Finalising...", "onecom-wp"),
				"msgFinished" =>__("Finished!", "onecom-wp"),
				"msgDelStg" =>__("Deleting staging...", "onecom-wp"),
			)
		);
	}

	/**
	 * Get Instance
	 * @return OneStaging
	 */
	public static function getInstance() {
		if( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Prevent cloning
	 * @return void
	 */
	private function __clone() {

	}

	/**
	 * Prevent unserialization
	 * @return void
	 */
	private function __wakeup() {

	}

	/**
	 * Load Dependencies
	 */
	private function loadDependencies() {
		// Set loader
		$this->set( "loader", new Loader() );

		// Set cache
		$this->set( "cache", new Cache() );

		// Set logger
		$this->set( "logger", new Logger() );

		// Set settings
		$this->set( "settings", new Settings() );

		// Set Administrator
		$admin = new Administrator( $this );
	}

	/**
	 * Execute Plugin
	 */
	public function run() {
		$this->get( "loader" )->run();
	}

	/**
	 * Set a variable to DI with given name
	 * @param string $name
	 * @param mixed $variable
	 * @return $this
	 */
	public function set( $name, $variable ) {
		// It is a function
		if( is_callable( $variable ) )
			$variable = $variable();

		// Add it to services
		$this->services[$name] = $variable;

		return $this;
	}

	/**
	 * Get given name index from DI
	 * @param string $name
	 * @return mixed|null
	 */
	public function get( $name ) {
		return (isset( $this->services[$name] )) ? $this->services[$name] : null;
	}

	/**
	 * @return string
	 */
	public static function getVersion() {
		if(is_admin()):
			$plugin_data = get_plugin_data( __FILE__);
			return ONECOM_WP_VERSION;
		else:
			$plugin_data = get_file_data(ONECOM_WP_PATH, array('Version' => 'Version'), false);
		endif;
		return ONECOM_WP_VERSION;
	}

	/**
	 * @return string
	 */
	public function getName() {
		$plugin_data = get_plugin_data( ONECOM_WP_PATH);
		return $plugin_data['Name'];
	}

	/**
	 * @return string
	 */
	public static function getSlug() {
		return dirname(__FILE__).DIRECTORY_SEPARATOR;
	}

	/**
	 * Get path to main plugin file
	 * @return string
	 */
	public function getPath() {
		return dirname( dirname( __FILE__ ) );
	}

	/**
	 * Get main plugin url
	 * @return string
	 */
	public function getUrl() {
		return plugin_dir_url( dirname( __FILE__ ) );
	}

	/**
	 * @return array|mixed|object
	 */
	public function getCPULoadSetting() {
		$options = $this->get( "settings" );
		$setting = $options->getCpuLoad();

		switch ( $setting ) {
			case "high":
				$cpuLoad = 0;
				break;

			case "medium":
				$cpuLoad = 1000;
				break;

			case "low":
				$cpuLoad = 3000;
				break;

			case "default":
			default:
				$cpuLoad = 1000;
		}

		return $cpuLoad;
	}
}
?>