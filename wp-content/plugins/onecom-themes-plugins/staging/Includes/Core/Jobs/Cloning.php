<?php
namespace OneStaging\Core\Jobs;

defined( "WPINC" ) or die(); // No Direct Access

use OneStaging\Core\Jobs\Handles\JobNotFoundException;
use OneStaging\OneStaging;

/**
 * Class Cloning
 * @package OneStaging\Core\Jobs
 */
class Cloning extends Job
{
    /**
     * Initialize is called in \Job
     */
    public function initialize(){
        $this->db = OneStaging::getInstance()->get("wpdb");
    }

    /**
     * Save Chosen Cloning Settings
     * @return bool
     */
    public function save() {

	    if ( $this->options->isStagingSite === true ) {
		    if ( ! isset( $_REQUEST ) || ! isset( $_REQUEST["liveDirectory"] ) ) {
			    return false;
		    }
	    }
	    else{
		    if ( ! isset( $_REQUEST ) || ! isset( $_REQUEST["cloneID"] ) ) {
			    return false;
		    }
	    }

	    // Generate Options
	    // Get Clone Name (_dir_name)
	    if ( $this->options->isStagingSite === true ) {
		    $this->options->clone = $this->options->existingLive->directoryName;
	    }
	    else{
		    $this->options->clone = $_REQUEST["cloneID"];
		    $this->options->prefix = $_REQUEST["stgPrefix"];
	    }

	    // Set Clone Directory Name
        $this->options->cloneDirectoryName  = preg_replace("#\W+#", '-', strtolower($this->options->clone));

	    // Set Clone Number
	    if ( $this->options->isStagingSite === true ) {
		    $this->options->cloneNumber = -1;
	    }
	    else{
		    $this->options->cloneNumber = 1;
	    }

	    if($this->options->isStagingSite === true){
		    $this->options->prefix = $this->options->existingLive->prefix;
	    }
	    else{
		    $this->options->prefix = $this->getTablePrefix();
	    }


        //$this->options->prefix              = $this->getTablePrefix();
        $this->options->includedDirectories = array();
        $this->options->excludedDirectories = array();
        $this->options->extraDirectories    = array();
        $this->options->excludedFiles       = array('.htaccess', '.DS_Store', '.git', '.svn', '.tmp', 'desktop.ini', '.gitignore', '.log', '.idea');

        // Job
        $this->options->job                 = new \stdClass();

        // Check if clone data already exists and use that one
        if (isset($this->options->existingClones[$this->options->clone]) )
        {
            
            $this->options->cloneNumber = $this->options->existingClones[$this->options->clone]->number;
            
            $this->options->prefix = isset($this->options->existingClones[$this->options->clone]->prefix) ? 
                    $this->options->existingClones[$this->options->clone]->prefix : 
                    $this->getTablePrefix();  
            
        }   // Clone does not exist but there are other clones in db
            // Get data and increment it
        elseif (!empty($this->options->existingClones))
        {
        	if( $this->options->existingClones instanceof \Countable ){
		        $this->options->cloneNumber =  count($this->options->existingClones)+1;
	        }
            $this->options->prefix = $this->getTablePrefix();
        }

        // Excluded Tables
        if (isset($_POST["excludedTables"]) && is_array($_POST["excludedTables"]))
        {
            $this->options->excludedTables = $_POST["excludedTables"];
        }

        // Excluded Directories
        if (isset($_POST["excludedDirectories"]) && is_array($_POST["excludedDirectories"]))
        {
            $this->options->excludedDirectories = $_POST["excludedDirectories"];
        }
        
        // Excluded Directories TOTAL
        // Do not copy these folders and plugins
        $excludedDirectories = array(
            ABSPATH . 'wp-content' . DIRECTORY_SEPARATOR . 'cache',
            ABSPATH . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'wps-hide-login',
            ABSPATH . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'wp-super-cache',
            );

		// Prevent existing STAGING Folder(s) to be copied when running LIVE_to_STAGING
        if($this->options->isStagingSite !== true){
        	foreach ($this->options->existingClones as $name=>$clone){
		        $excludedDirectories[] = $this->options->existingClones[$name]['path'];
	        }
	        $excludedDirectories[] = ABSPATH.$this->options->clone;
        }

        $this->options->excludedDirectories = array_merge($excludedDirectories, $this->options->excludedDirectories);

        // Included Directories
        if (isset($_POST["includedDirectories"]) && is_array($_POST["includedDirectories"]))
        {
            $this->options->includedDirectories = $_POST["includedDirectories"];
        }

        // Extra Directories
        if (isset($_POST["extraDirectories"]) && !empty($_POST["extraDirectories"]) )
        {      
            $this->options->extraDirectories = $_POST["extraDirectories"];
        }

        // Directories to Copy
        $this->options->directoriesToCopy = array_merge(
            $this->options->includedDirectories,
            $this->options->extraDirectories
        );

        array_unshift($this->options->directoriesToCopy, ABSPATH);
                
        // Delete files to copy listing
        $this->cache->delete("files_to_copy");

        return $this->saveOptions();
    }

    /**
     * Create a new staging prefix which does not already exists in database
     */
    public function getTablePrefix(){

    	if($this->options->isStagingSite === true){
		    return $this->options->existingLive->prefix;
	    }

	    else if(isset($_REQUEST["stgPrefix"]) && strlen($_REQUEST["stgPrefix"])){
		    $this->options->prefix = $_REQUEST["stgPrefix"];

		    $sql = "SHOW TABLE STATUS LIKE '{$this->options->prefix}%'";
		    $tables = $this->db->get_results($sql);

		    if($tables){
			    // Tables already exist with this prefix.
			    // NOTE: We are force-overwriting existing staging tables here.
			    // We have ensured that our staging prefix is going to be domain-based hash.
			    // This hash will be unique for every WP website and so will be the prefix.
			    // We should overwrite the tables because this will be the case when a broken staging will get recreated freshly.
			    // E.g., If user Resets WordPress or does a manual editing in database.
			    $this->log("Warning: Found existing tables having staging prefix. '{$this->options->prefix}'! Proceeding to overwrite them.");
		    }
		    return $this->options->prefix;
	    }
    }
    
    /**
     * Check if potential new prefix of staging site would be identical with live site. 
     * @return boolean
     */
    private function isPrefixIdentical(){
        $db = OneStaging::getInstance()->get("wpdb");
        
        $livePrefix = $db->prefix;
        $stagingPrefix = $this->options->stagingPrefix;
        
        if ($livePrefix == $stagingPrefix){
            return true;
        }
        return false;
    }

	/**
     * Start the cloning job
     */
    public function start()
    {
        if (null === $this->options->currentJob)
        {
            $this->log("Cloning job for {$this->options->clone} finished");
            return true;
        }

        $methodName = "job" . ucwords($this->options->currentJob);

	    $this->log("Executing job {$this->options->currentJob}");

        if (!method_exists($this, $methodName))
        {
            $this->log("Can't execute job; Job's method {$methodName} is not found");
            throw new JobNotFoundException($methodName);
        }

        // Call the job
        return $this->{$methodName}();
    }

    /**
     * @param object $response
     * @param string $nextJob
     * @return object
     */
	private function handleJobResponse($response, $nextJob)
	{
		// Job is not done
		if (true !== $response->status)
		{
			return $response;
		}

		$this->options->currentJob              = $nextJob;
		$this->options->currentStep             = 0;
		$this->options->totalSteps              = 0;

		// Save options
		$this->saveOptions();

		return $response;
	}

    /**
     * Clone Database
     * @return object
     */
    public function jobDatabase()
    {
        $database = new Database();
        return $this->handleJobResponse($database->start(), "directories");
    }

    /**
     * Get All Files From Selected Directories Recursively Into a File
     * @return object
     */
    public function jobDirectories()
    {
        $directories = new Directories();
        return $this->handleJobResponse($directories->start(), "files");
    }

    /**
     * Copy Files
     * @return object
     */
    public function jobFiles()
    {
        $files = new Files();
        return $this->handleJobResponse($files->start(), "data");
    }

    /**
     * Replace Data
     * @return object
     */
    public function jobData()
    {
        $data = new Data();
        return $this->handleJobResponse($data->start(), "finish");
    }

    /**
     * Save Clone Data
     * @return object
     */
    public function jobFinish()
    {
        $finish = new Finish();
        return $this->handleJobResponse($finish->start(), '');
    }
}