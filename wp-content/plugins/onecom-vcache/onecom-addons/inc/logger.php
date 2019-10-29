<?php
/**
 * Class Logger
 */
if( ! class_exists( 'Logger' ) ) { 
    class Logger
    {
        const TYPE_ERROR    = "ERROR";

        const TYPE_CRITICAL = "CRITICAL";

        const TYPE_FATAL    = "FATAL";

        const TYPE_WARNING  = "WARNING";

        const TYPE_INFO     = "STATE";
        
        const TYPE_DEBUG    = "DEBUG";

        const TYPE_STATUS   = "STATUS";

        private $middleware;
        private $middlewareVer = 'v1.0';
        private $middlewareEndpoint = 'log';


        /**
         * Log directory (full path)
         * @var string
         */
        private $logDir;

        /**
         * Log file extension
         * @var string
         */
        private $logExtension   = "log";

        /**
         * Messages to log
         * @var array
         */
        private $messages       = array();

        /**
         * Forced filename for the log
         * @var null|string
         */
        private $fileName       = null;

        /**
         * Logger constructor.
         * @param null|string $logDir
         * @param null|string $logExtension
         * @throws \Exception
         */
        public function __construct(){
            if( isset( $_SERVER[ 'ONECOM_WP_ADDONS_API' ] ) && $_SERVER[ 'ONECOM_WP_ADDONS_API' ] != '' ) {
                $ONECOM_WP_ADDONS_API = $_SERVER[ 'ONECOM_WP_ADDONS_API' ];
            } elseif( defined( 'ONECOM_WP_ADDONS_API' ) && ONECOM_WP_ADDONS_API != '' && ONECOM_WP_ADDONS_API != false ) {
                $ONECOM_WP_ADDONS_API = ONECOM_WP_ADDONS_API;
            } else {
                $ONECOM_WP_ADDONS_API = 'http://wpapi.one.com/';
            }
            $ONECOM_WP_ADDONS_API = rtrim( $ONECOM_WP_ADDONS_API, '/' );
            $this->middleware = $ONECOM_WP_ADDONS_API.'/api/'.$this->middlewareVer.'/'.$this->middlewareEndpoint;
            $this->logDir = wp_upload_dir()[ 'basedir' ] . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;
            $this->init();
        }

        public function init($logDir = null, $logExtension = null) {
            // Set log directory
            if (!empty($logDir) && is_dir($logDir)) {
                $this->logDir = rtrim($logDir, "/\\") . DIRECTORY_SEPARATOR;
            }

            // Set log extension
            if (!empty($logExtension)) {
                $this->logExtension = $logExtension;
            }

            // If cache directory doesn't exists, create it
            if (!is_dir($this->logDir) && !@mkdir($this->logDir, 0775, true)) {
                throw new \Exception("Failed to create log directory!");
            }
        }

        /**
         * @param string $message
         * @param string $type
         */
        public function log($message, $type = self::TYPE_ERROR)
        {
            $this->add($message, $type);
            $this->commit();
        }

        /**
         * @param string $message
         * @param string $type
         */
        public function add($message, $type = self::TYPE_ERROR)
        {  
            $this->messages[] = array(
                "type"      => $type,
                "date"      => date("Y/m/d H:i:s"),
                "message"   => $message
            );
        }

        /**
         * @return null|string
         */
        public function getFileName()
        {
            if( $this->fileName == '' ) {
                return 'error';
            }
            return $this->fileName;
        }

        /**
         * @param string $fileName
         */
        public function setFileName($fileName)
        {
            $this->fileName = $fileName;
        }

        /**
         * @return bool
         */
        public function commit()
        {
            //$this->init();
            if (empty($this->messages))
            {
                return true;
            }

            $messageString = '';
            foreach ($this->messages as $message)
            {
                $messageString .= "[{$message["type"]}]-[{$message["date"]}] {$message["message"]}".PHP_EOL;
            }

            $this->messages = array();

            if (1 > strlen($messageString))
            {
                return true;
            }
            return (@file_put_contents($this->getLogFile(), $messageString, FILE_APPEND | LOCK_EX));
        }

        /**
         * @param null|string $file
         * @return string
         */
        public function read($file = null)
        {
            return @file_get_contents($this->getLogFile($file));
        }

        /**
         * @param null|string $fileName
         * @return string
         */
        public function getLogFile($fileName = null)
        {
            //$this->init();
            // Default
            if (null === $fileName)
            {
                $fileName = (null !== $this->fileName) ? $this->fileName : date("Y_m_d");
            }

            return $this->logDir . $fileName . '.' . $this->logExtension;
        }

        /**
         * Delete a log file
         * @param string $logFileName
         * @return bool
         * @throws \Exception
         */
        public function delete($logFileName)
        {
            //$this->init();
            $logFile = $this->logDir . $logFileName . '.' . $this->logExtension;

            if (false === @unlink($logFile))
            {
                throw new \Exception("Couldn't delete cache: {$logFileName}. Full Path: {$logFile}");
            }

            return true;
        }

        /**
         * @return string
         */
        public function getLogDir()
        {
            return $this->logDir;
        }

        /**
         * @return string
         */
        public function getLogExtension()
        {
            return $this->logExtension;
        }
        
        /**
         * Get last element of logging data array
         * @return string
         */
        public function getLastLogMsg()
        {
            // return all messages
            if (count ($this->messages) > 1){
                return $this->messages;
            }else{
                // Return last message
                return $this->messages[]=array_pop($this->messages);
            }
        }
        
        /**
         * Get running time in seconds
         * @return int
         */
        public function getRunningTime(){
            $str_time = $this->messages[0]["date"];
            return $str_time;
        }

    	/**
    	 * Generic log to WP API
    	 * @param string entry_prefix // unique prefix to indetify the plugin or theme
    	 * @param string action_type // 
    	 * @param string message // message for log
         * @param string version // version of plugin or theme
         * @param boolean error // is log having error or not
    	 * @return bool
    	 **/
    	public function wpAPISendLog( $entry_prefix = 'general_', $action_type, $message = '', $version = null, $error = 'false' ) {
            if( '' === $action_type || null === $action_type ) {
                return;
            }
            $error = (string) $error;
    		$log_url = $this->middleware;

            $entry_prefix = rtrim( $entry_prefix, '_' ) . '_';

    		$params = array(
    			'action_type' => $entry_prefix . filter_var( $action_type, FILTER_SANITIZE_STRING ),
    			'message' => $message,
    			'error' => $error
    		);
            
            if( null !== $version ) {
                $params[ 'version' ] = $version;
                $params[ 'message' ] .= ' | '. 'Version:'.$version;
            }

    		$client_ip = $this->onecom_get_client_ip_env();
    		$client_domain = ( isset( $_SERVER[ 'ONECOM_DOMAIN_NAME' ] ) && ! empty( $_SERVER[ 'ONECOM_DOMAIN_NAME' ] ) ) ? $_SERVER[ 'ONECOM_DOMAIN_NAME' ] : 'localhost';

            global $wp_version;

            $log_entry = json_encode( $params );

        	$save_log = wp_safe_remote_post( $log_url , array(
                    'method'        => 'POST',
                    'timeout'       => 3,
                    'user-agent'    => 'WordPress/' . $wp_version . '; ' . home_url(),
                    'compress'      => false,
                    'decompress'    => true,
                    'sslverify'     => true,
                    'stream'        => false,
                    'body'          => $log_entry,
                    'headers'       => array(
                        'X-ONECOM-CLIENT-IP'     => $client_ip,
                        'X-ONECOM-CLIENT-DOMAIN' => $client_domain
                    )
                )
            );

            if ( ! is_wp_error( $save_log ) ) {
                return true;
            } else {
                return false;
            }
    	}

        /**
         * Function to get the client ip address
         **/
        public function onecom_get_client_ip_env() {
            if (getenv('HTTP_CLIENT_IP'))
                $ipaddress = getenv('HTTP_CLIENT_IP');
            else if(getenv('REMOTE_ADDR'))
                $ipaddress = getenv('REMOTE_ADDR');
            else
                $ipaddress = '0.0.0.0';
         
            return $ipaddress;
        }
    }
}