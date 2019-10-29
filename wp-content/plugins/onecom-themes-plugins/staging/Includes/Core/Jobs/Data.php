<?php
namespace OneStaging\Core\Jobs;

defined( "WPINC" ) or die(); // No Direct Access

use OneStaging\Core\Logger;
use OneStaging\OneStaging;

/**
 * Class Data
 * @package OneStaging\Core\Jobs
 */
class Data extends JobExecutable
{

    /**
     * @var \wpdb
     */
    private $db;

    /**
     * @var string
     */
    private $prefix;

	/**
	 * @var \wp
	 */
	private $parentData;


	/**
     * Initialize
     */
    public function initialize()
    {
        $this->db       = OneStaging::getInstance()->get("wpdb");

        //$this->prefix   = "wpstg{$this->options->cloneNumber}_";
	    $this->prefix   = $this->options->prefix;

	    $this->parentData = $this->options->parentData;


	    // Fix current step
        if (0 == $this->options->currentStep)
        {
            $this->options->currentStep = 1;
        }
    }

    /**
     * Calculate Total Steps in This Job and Assign It to $this->options->totalSteps
     * @return void
     */
    protected function calculateTotalSteps()
    {
        $this->options->totalSteps = 9;
    }

    /**
     * Start Module
     * @return object
     */
    public function start()
    {
        // Execute steps
        $this->run();

        // Save option, progress
        $this->saveOptions();

        // Prepare response
        $this->response = array(
            "status"        => true,
            "percentage"    => 100,
            "total"         => $this->options->totalSteps,
            "step"          => $this->options->totalSteps,
            "last_msg"      => $this->logger->getLastLogMsg(),
            "running_time"  => $this->time() - time(),
            "job_done"      => true
        );

        return (object) $this->response;
    }

    /**
     * Execute the Current Step
     * Returns false when over threshold limits are hit or when the job is done, true otherwise
     * @return bool
     */
    protected function execute()
    {
        // Fatal error. Let this happen never and break here immediately
        if ($this->isRoot()){
            return false;
        }

        // Over limits threshold
        if ($this->isOverThreshold())
        {
            // Prepare response and save current progress
            $this->prepareResponse(false, false);
            $this->saveOptions();
            return false;
        }

        // No more steps, finished
        if ($this->isFinished())
        {
            $this->prepareResponse(true, false);
            return false;
        }

        // Execute step
        $stepMethodName = "step" . $this->options->currentStep;
        if (!$this->{$stepMethodName}())
        {
            $this->prepareResponse(false, false);
            return false;
        }

        // Prepare Response
        $this->prepareResponse();

        // Not finished
        return true;
    }

    /**
     * Checks Whether There is Any Job to Execute or Not
     * @return bool
     */
    private function isFinished()
    {
        return (
            $this->options->currentStep > $this->options->totalSteps ||
            !method_exists($this, "step" . $this->options->currentStep)
        );
    }
    
    /**
     * Check if current operation is done on the root folder or on the live DB
     * @return boolean
     */
    private function isRoot(){
        
        // Prefix is the same as the one of live site
        $wpdb = OneStaging::getInstance()->get("wpdb");
        if ($wpdb->prefix === $this->prefix){
            return true;
        }
        
        // CloneName is empty
        $name = (array)$this->options->cloneDirectoryName;
        if (empty($name)){
            return true;
        }
        
        // Live Path === Staging path
        if (get_home_url() . $this->options->cloneDirectoryName === get_home_url()){
            return true;
        }
        
        return false;
    }

        
    /**
     * Check if table exists
     * @param string $table
     * @return boolean
     */
    protected function isTable($table){
      if($this->db->get_var("SHOW TABLES LIKE '{$table}'") != $table ){
         $this->log( "Table {$table} does not exists", Logger::TYPE_ERROR );
         return false;
      }
      return true;
    }

    /**
     * Replace "siteurl"
     * @return bool
     */
    protected function step1() {
      $this->log( "Search & Replace: Updating siteurl and homeurl in {$this->prefix}options {$this->db->last_error}", Logger::TYPE_INFO );

      if( false === $this->isTable( $this->prefix . 'options' ) ) {
         return true;
      }

      // Installed in sub-directory
      if( isset( $this->settings->wpSubDirectory ) && "1" === $this->settings->wpSubDirectory ) {
         $subDirectory = str_replace( get_home_path(), '', ABSPATH );

         if($this->options->isStagingSite === true){
	         $newURL = $this->options->existingLive->url . '/' . $subDirectory;
         }
         else{
	        $newURL = get_home_url() . '/' . $subDirectory . $this->options->cloneDirectoryName;
         }


         $this->log( "Updating siteurl and homeurl to " . get_home_url() . '/' . $subDirectory . $this->options->cloneDirectoryName );
         // Replace URLs
         $result = $this->db->query(
             $this->db->prepare(
                     "UPDATE {$this->prefix}options SET option_value = %s WHERE option_name = 'siteurl' or option_name='home'", $newURL
             )
         );
      }
      else {

	      if($this->options->isStagingSite === true){ $newURL = $this->options->existingLive->url; }

	      else{ $newURL = get_home_url() . '/' . $this->options->cloneDirectoryName; }

         $this->log( "Search & Replace:: Updating siteurl and homeurl to " . get_home_url() . '/' . $this->options->cloneDirectoryName );
         // Replace URLs
         $result = $this->db->query(
                 $this->db->prepare(
                         "UPDATE {$this->prefix}options SET option_value = %s WHERE option_name = 'siteurl' or option_name='home'", $newURL
                 )
         );
      }


      // All good
      if( $result ) {
         return true;
      }

      $this->log( "Search & Replace: Failed to update siteurl and homeurl in {$this->prefix}options {$this->db->last_error}", Logger::TYPE_ERROR );

	    (function_exists( 'onecom_generic_log')? onecom_generic_log( 'staging_error', "Search & Replace failed to update siteurl and homeurl in {$this->prefix}options {$this->db->last_error}", 'staging_error' ):'');

      return false;
   }

   /**
     * Update "onecom_is_staging_site"
     * @return bool
     */
    protected function step2()
    {
	    $this->log( "Search & Replace: Inserting/Updating row onecom_is_staging_site in {$this->prefix}options {$this->db->last_error}" );

		if( false === $this->isTable( $this->prefix . 'options' ) ) {
		 return true;
		}

		// STAGING to LIVE
	    if($this->options->isStagingSite == true) {
		    $result = $this->db->query(
			    $this->db->prepare(
				    "UPDATE {$this->prefix}options SET option_value = %s WHERE option_name = 'onecom_is_staging_site'",
				    false
			    )
		    );

		    // If UPDATE failed without error then INSERT
		    if ( '' === $this->db->last_error && 0 == $result ) {
			    $result = $this->db->query(
				    $this->db->prepare(
					    "INSERT INTO {$this->prefix}options (option_name,option_value) VALUES ('onecom_is_staging_site',%s)",
					    false
				    )
			    );
		    }
	    }

	    // LIVE TO STAGING
	    else{
		    $result = $this->db->query(
			    $this->db->prepare(
				    "UPDATE {$this->prefix}options SET option_value = %s WHERE option_name = 'onecom_is_staging_site'",
				    true
			    )
		    );

		    // If UPDATE failed without error then INSERT
		    if ( '' === $this->db->last_error && 0 == $result ) {
			    $result = $this->db->query(
				    $this->db->prepare(
					    "INSERT INTO {$this->prefix}options (option_name,option_value) VALUES ('onecom_is_staging_site',%s)",
					    true
				    )
			    );
		    }
	    }

	    // All good
	    if ($result){
            return true;
        }

        $this->log("Search & Replace: Failed to update onecom_is_staging_site in {$this->prefix}options {$this->db->last_error}", Logger::TYPE_ERROR);

	    (function_exists( 'onecom_generic_log')? onecom_generic_log( 'staging_error', "Search & Replace: Failed to update onecom_is_staging_site in {$this->prefix}options {$this->db->last_error}", 'staging_error' ):'');

        return false;
    }


	/**
	 * Add/Update metadata of live/staging
	 * @return bool
	 */
	protected function step3()
	{
		// SAVE the Existing_LIVE website data only when LIVE_to_STAGING.
		if($this->options->isStagingSite !== true) {

			// Save new clone data in the cloned website database [It will be used at the time of deployment from Staging to Live]
			$result = $this->db->query(
				$this->db->prepare(
					"UPDATE {$this->prefix}options SET option_value = %s WHERE option_name = 'onecom_staging_existing_live'",
					serialize($this->options->parentData)
				)
			);

			// No errors but no option name such as onecom_staging_existing_live
			if ( '' === $this->db->last_error && 0 == $result ) {
				$result = $this->db->query(
					$this->db->prepare(
						"INSERT INTO {$this->prefix}options (option_name,option_value) VALUES ('onecom_staging_existing_live',%s)",
						serialize($this->parentData)
					)
				);
			}
		}
		// SAVE Existing staging data when Deploying STAGING_TO_LIVE.
		else{
			$existingStaging = [];
			$existingStaging[basename(get_home_path())] = (array) $this->options->parentData;
			$result = $this->db->query(
				$this->db->prepare(
					"UPDATE {$this->prefix}options SET option_value = %s WHERE option_name = 'onecom_staging_existing_staging'",
					serialize($existingStaging)
				)
			);

			// No errors but no option name such as onecom_staging_existing_live
			if ( '' === $this->db->last_error && 0 == $result ) {
				$result = $this->db->query(
					$this->db->prepare(
						"INSERT INTO {$this->prefix}options (option_name,option_value) VALUES ('onecom_staging_existing_staging',%s)",
						serialize($existingStaging)
					)
				);
			}
		}


		// REMOVE the Existing_Staging records from STAGING_options_table
		if($this->options->isStagingSite !== true){
			$result_delete = $this->db->query(
				$this->db->prepare(
					"DELETE FROM {$this->prefix}options WHERE option_name = %s",
					"onecom_staging_existing_staging"
				)
			);

			if (!$result_delete){
				$this->log("Finish: Failed to remove Existing Staging data from {$this->options->clone}'s database'");
			}
		}
		// REMOVE the Existing_Live records from LIVE_options_table
		else{
			$result_delete = $this->db->query(
				$this->db->prepare(
					"DELETE FROM {$this->prefix}options WHERE option_name = %s",
					"onecom_staging_existing_live"
				)
			);
			if (!$result_delete){
				$this->log("Finish: Failed to remove Existing Live data from {$this->options->clone}'s database'");
			}
		}
		// All good
		if ($result){
			return true;
		}

		$this->log("Search & Replace: Failed to update/delete onecom_staging_existing_staging / onecom_staging_existing_live in {$this->prefix}options {$this->db->last_error}", Logger::TYPE_ERROR);

		(function_exists( 'onecom_generic_log')? onecom_generic_log( 'staging_error', "Search & Replace: Failed to update/delete onecom_staging_existing_staging / onecom_staging_existing_live in {$this->prefix}options {$this->db->last_error}", 'staging_error' ):'');

		return false;
	}

    /**
     * Update rewrite_rules
     * @return bool
     */
    protected function step4()
    {
        $this->log("Search & Replace: Updating rewrite_rules in {$this->prefix}options {$this->db->last_error}");
        
	      if( false === $this->isTable( $this->prefix . 'options' ) ) {
	         return true;
	      }

        $result = $this->db->query(
            $this->db->prepare(
                "UPDATE {$this->prefix}options SET option_value = %s WHERE option_name = 'rewrite_rules'",
                ''
            )
        );

        // All good
        if ($result){
            return true;
        }

        $this->log("Failed to update rewrite_rules in {$this->prefix}options {$this->db->last_error}", Logger::TYPE_ERROR);

	    (function_exists( 'onecom_generic_log')? onecom_generic_log( 'staging_warning', "Failed to update rewrite_rules in {$this->prefix}options {$this->db->last_error}", 'staging_error' ):'');

        return true;
    }
    /**
     * Update permalink structure to default
     * @return bool
     */
    protected function step5()
    {
    	// Reset permalink structure only when copying from LIVE to STAGING.
	    if($this->options->isStagingSite === true)
	    	return true;

        $this->log("Search & Replace: Reset permalink_structure in {$this->prefix}options {$this->db->last_error}");

	      if( false === $this->isTable( $this->prefix . 'options' ) ) {
	         return true;
	      }

        $result = $this->db->query(
            $this->db->prepare(
                "UPDATE {$this->prefix}options SET option_value = %s WHERE option_name = 'permalink_structure'",
                ''
            )
        );

        // All good
        if ($result){
            return true;
        }

        $this->log("Failed to Reset permalink_structure in {$this->prefix}options {$this->db->last_error}", Logger::TYPE_ERROR);

	    (function_exists( 'onecom_generic_log')? onecom_generic_log( 'staging_warning', "Failed to Reset permalink_structure in {$this->prefix}options {$this->db->last_error}", 'staging_error' ):'');

        return true;
    }

    /**
     * Update Table Prefix in meta_keys
     * @return bool
     */
    protected function step6() {
        $this->log( "Search & Replace: Updating {$this->prefix}usermeta db prefix {$this->db->last_error}" );

      if( false === $this->isTable( $this->prefix . 'usermeta' ) ) {
         return true;
      }

	    $resultOptions = $this->db->query(
	            $this->db->prepare(
	                    "UPDATE {$this->prefix}usermeta SET meta_key = replace(meta_key, %s, %s) WHERE meta_key LIKE %s", $this->db->prefix, $this->prefix, $this->db->prefix . "_%"
	            )
	    );

        if( !$resultOptions ) {
            $this->log( "Search & Replace: Failed to update usermeta meta_key database table prefixes; {$this->db->last_error}", Logger::TYPE_ERROR );
            return false;
        }

        $this->log( "Updating {$this->prefix}options, option_name database table prefixes; {$this->db->last_error}" );

        $resultUserMeta = $this->db->query(
                $this->db->prepare(
                        "UPDATE {$this->prefix}options SET option_name= replace(option_name, %s, %s) WHERE option_name LIKE %s", $this->db->prefix, $this->prefix, $this->db->prefix . "_%"
                )
        );

        if( !$resultUserMeta ) {
            $this->log( "Search & Replace: Failed to update options, option_name database table prefixes; {$this->db->last_error}", Logger::TYPE_ERROR );

	        (function_exists( 'onecom_generic_log')? onecom_generic_log( 'staging_error', "Search & Replace: Failed to update options, option_name database table prefixes; {$this->db->last_error}", 'staging_error' ):'');

	        return false;
        }

        return true;
    }

    /**
     * Update $table_prefix in wp-config.php
     * @return bool
     */
    protected function step7()
    {
		if($this->options->isStagingSite === true){
			$path = $this->options->existingLive->path . "/wp-config.php";
		}
		else{
			$path = ABSPATH . $this->options->cloneDirectoryName . "/wp-config.php";
		}

        $this->log("Search & Replace: Updating table_prefix in {$path} to " . $this->prefix);
        if (false === ($content = file_get_contents($path)))
        {
            $this->log("Search & Replace: Failed to update table_prefix in {$path}. Can't read contents", Logger::TYPE_ERROR);
            return false;
        }

        // Replace table prefix
        $content = str_replace('$table_prefix', '$table_prefix = \'' . $this->prefix . '\';//', $content);

        // Replace URLs
        $content = str_replace(get_home_url(), get_home_url() . '/' . $this->options->cloneDirectoryName, $content);

        if (false === @file_put_contents($path, $content))
        {
            $this->log("Search & Replace: Failed to update {$table_prefix} in {$path} to " .$this->prefix . ". Can't save contents", Logger::TYPE_ERROR);
            return false;
        }

        return true;
    }

    /**
     * Reset index.php to original file
     * Check first if main wordpress is used in subfolder and index.php in parent directory
     * @see: https://codex.wordpress.org/Giving_WordPress_Its_Own_Directory
     * @return bool
     */
    protected function step8()
    {
        // No settings, all good
        if (!isset($this->settings->wpSubDirectory) || "1" !== $this->settings->wpSubDirectory)
        {
            $this->log("Search & Replace: WP installation is not in a subdirectory!");
            return true;
        }

        $path = ABSPATH . $this->options->cloneDirectoryName . "/index.php";

        if (false === ($content = file_get_contents($path)))
        {
            $this->log("Search & Replace: Failed to reset {$path} for sub directory; can't read contents", Logger::TYPE_ERROR);
            return false;
        }


        if (!preg_match("/(require(.*)wp-blog-header.php' \);)/", $content, $matches))
        {
            $this->log(
                "Search & Replace: Failed to reset index.php for sub directory; wp-blog-header.php is missing",
                Logger::TYPE_ERROR
            );
            return false;
        }

        $pattern = "/require(.*) dirname(.*) __FILE__ (.*) \. '(.*)wp-blog-header.php'(.*);/";

        $replace = "require( dirname( __FILE__ ) . '/wp-blog-header.php' ); // " . $matches[0];
        $replace.= " // Changed by One.com Staging";

        if (null === preg_replace($pattern, $replace, $content))
        {
            $this->log("Search & Replace: Failed to reset index.php for sub directory; replacement failed", Logger::TYPE_ERROR);
            return false;
        }

        if (false === @file_put_contents($path, $content))
        {
            $this->log("Search & Replace: Failed to reset index.php for sub directory; can't save contents", Logger::TYPE_ERROR);
            return false;
        }

        return true;
    }
	protected function step9()
	{
		// Live to Staging
		if($this->options->isStagingSite !== true){
			// Turn OFF search engine indexing
			$result = $this->db->query(
				$this->db->prepare("UPDATE {$this->prefix}options SET option_value = %s WHERE option_name = 'blog_public'", 0)
			);

			if( !$result ) {
				$this->log( "Search & Replace: Failed to turn OFF search engine indexing; {$this->db->last_error}", Logger::TYPE_ERROR );
				return false;
			}
		}
		// Staging to Live
		else{
			// Turn ON search engine indexing, if it is OFF
			$result = $this->db->query(
				$this->db->prepare("UPDATE {$this->prefix}options SET option_value = %s WHERE option_name = 'blog_public'", 1)
			);

			if( !$result ) {
				$this->log( "Search & Replace: Failed to turn ON search engine indexing; {$this->db->last_error}", Logger::TYPE_ERROR );
				return false;
			}
		}
		return true;
	}
}