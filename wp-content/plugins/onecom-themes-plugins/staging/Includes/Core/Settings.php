<?php
namespace OneStaging\Core;

defined( "WPINC" ) or die(); // No Direct Access

/**
 * Class Settings
 * @package OneStaging\DTO
 */
class Settings {

	/**
	 * @var array
	 */
	public $_isStaging;
    /**
     * @var array
     */
    protected $_raw;

	/**
     * @var string
     */
    protected $domainHash;

    /**
     * @var int
     */
    protected $queryLimit;

    /**
     * @var int
     */
   protected $fileLimit;

   /**
    * @var int
    */
    protected $batchSize;

    /**
     * @var string
     */
    protected $cpuLoad;

    /**
     * @var bool
     */
    protected $unInstallOnDelete;

    /**
     * @var bool
     */
    protected $optimizer;

    /**
     * @var bool
     */
    protected $disableAdminLogin;

    /**
     * @var bool
     */
    protected $wpSubDirectory;

    /**
     * @var bool
     */
    protected $checkDirectorySize;

    /**
     * @var bool
     */
    protected $debugMode;

    /**
     * @var array
     */
    protected $blackListedPlugins = array();

    /**
     * Settings constructor.
     */
    public function __construct() {

      $this->_raw = unserialize( 'a:5:{s:10:"queryLimit";s:4:"1000";s:9:"fileLimit";s:1:"1";s:9:"batchSize";s:1:"2";s:7:"cpuLoad";s:6:"medium";s:18:"checkDirectorySize";s:1:"1";}');

      if (!empty($this->_raw)){
         $this->hydrate( $this->_raw );
      }

      $this->_isStaging = (bool) get_option( "onecom_is_staging_site" );

      // WP_URL based hash calculation
      $this->domainHash = hash_pbkdf2("sha256", self::urlToDomain( get_option( 'siteurl') ), 'onecom_staging', 100, 5);
    }

   /**
     * @param array $settings
     * @return $this
     */
   public function hydrate( $settings = array() ) {
        $this->_raw = $settings;

      foreach ( $settings as $key => $value ) {
         if( property_exists( $this, $key ) ) {
                $this->{$key} = $value;
         }
      }

        return $this;
    }

    /**
     * @return array
     */
   public function getRaw() {
        return $this->_raw;
    }

    /**
     * @return int
     */
   public function getQueryLimit() {
      return ( int ) $this->queryLimit;
    }

    /**
     * @param int $queryLimit
     */
   public function setQueryLimit( $queryLimit ) {
        $this->queryLimit = $queryLimit;
    }

    /**
     * @return int
     */
   public function getFileLimit() {
      return ( int ) $this->fileLimit;
    }

    /**
    * @param int $fileCopyLimit
    */
   public function setFileLimit( $fileLimit ) {
      $this->fileLimit = $fileLimit;
   }

   /**
    * @return int
    */
   public function getBatchSize() {
      return ( int ) $this->batchSize;
   }

   /**
     * @param int $batchSize
     */
   public function setBatchSize( $batchSize ) {
        $this->batchSize = $batchSize;
    }

    /**
     * @return string
     */
   public function getCpuLoad() {
        return $this->cpuLoad;
    }

    /**
     * @param string $cpuLoad
     */
   public function setCpuLoad( $cpuLoad ) {
        $this->cpuLoad = $cpuLoad;
    }

    /**
     * @return bool
     */
   public function isUnInstallOnDelete() {
        return ('1' === $this->unInstallOnDelete);
    }

    /**
     * @param bool $unInstallOnDelete
     */
   public function setUnInstallOnDelete( $unInstallOnDelete ) {
        $this->unInstallOnDelete = $unInstallOnDelete;
    }

    /**
     * @return bool
     */
   public function isOptimizer() {
        return ('1' === $this->optimizer);
    }

    /**
     * @param bool $optimizer
     */
   public function setOptimizer( $optimizer ) {
        $this->optimizer = $optimizer;
    }

    /**
     * @return bool
     */
   public function isDisableAdminLogin() {
        return ('1' === $this->disableAdminLogin);
    }

    /**
     * @param bool $disableAdminLogin
     */
   public function setDisableAdminLogin( $disableAdminLogin ) {
        $this->disableAdminLogin = $disableAdminLogin;
    }

    /**
     * @return bool
     */
   public function isWpSubDirectory() {
        return ('1' === $this->wpSubDirectory);
    }

    /**
     * @param bool $wpSubDirectory
     */
   public function setWpSubDirectory( $wpSubDirectory ) {
        $this->wpSubDirectory = $wpSubDirectory;
    }

    /**
     * @return bool
     */
   public function isCheckDirectorySize() {
        return ('1' === $this->checkDirectorySize);
    }

    /**
     * @param bool $checkDirectorySize
     */
   public function setCheckDirectorySize( $checkDirectorySize ) {
        $this->checkDirectorySize = $checkDirectorySize;
    }

    /**
     * @return bool
     */
   public function isDebugMode() {
        return ('1' === $this->debugMode);
    }

    /**
     * @param bool $debugMode
     */
   public function setDebugMode( $debugMode ) {
        $this->debugMode = $debugMode;
    }

    /**
     * @return array
     */
   public function getBlackListedPlugins() {
        return $this->blackListedPlugins;
    }

    /**
     * @param array $blackListedPlugins
     */
   public function setBlackListedPlugins( $blackListedPlugins ) {
        $this->blackListedPlugins = $blackListedPlugins;
    }

	/**
	 * @param $url string
	 * @return string
	 */
	public function urlToDomain($url) {
		if ( substr($url, 0, 8) == 'https://' ) {
			$url = substr($url, 8);
		}
		if ( substr($url, 0, 7) == 'http://' ) {
			$url = substr($url, 7);
		}
		if ( substr($url, 0, 4) == 'www.' ) {
			$url = substr($url, 4);
		}
		return $url;
	}
	/**
	 * @return boolean
	 */
	public function isStagingSite(){
		$this->_isStaging = (bool) get_option( "onecom_is_staging_site" );
		return $this->_isStaging;
	}

	/**
	 * @return string staging_hash
	 */
	public function getStagingDir(){
		return "stg_" . $this->domainHash;
	}

	/**
	 * @return string $stagingPrefix
	 */
	public function getStagingPrefix(){
		return 'stg_' . $this->domainHash. "_";
	}
}