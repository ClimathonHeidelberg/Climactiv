<?php
namespace OneStaging\Core\Jobs;

defined( "WPINC" ) or die(); // No Direct Access

use OneStaging\Core\Jobs\Handles\JobNotFoundException;
use OneStaging\OneStaging;

/**
 * Class Cloning
 * @package OneStaging\Core\Jobs
 */
class Updating extends Job
{
	/**
     * Initialize is called in \Job
     */
    public function initialize(){
	    $this->rebuilding = true;
        $this->db = OneStaging::getInstance()->get("wpdb");
    }

    /**
     * Save Chosen Cloning Settings
     * @return bool
     */
    public function save()
    {
        if (!isset($_POST) || !isset($_POST["cloneID"]))
        {
            return false;
        }

        // Generate Options
        // Clone
        $this->options->clone               = $_POST["cloneID"];
        $this->options->stagingPrefix       = ((isset($_POST["stgPrefix"])? $_POST["stgPrefix"]: ''));
        $this->options->cloneDirectoryName  = preg_replace("#\W+#", '-', strtolower($this->options->clone));
        $this->options->cloneNumber         = 1;
        $this->options->includedDirectories = array();
        $this->options->excludedDirectories = array();
        $this->options->extraDirectories    = array();
        $this->options->excludedFiles       = array('.htaccess', '.DS_Store', '.git', '.svn', '.tmp', 'desktop.ini', '.gitignore', '.log');

        // Job
        $this->options->job                 = new \stdClass();

        // Check if clone data already exists and use that one
        if (isset($this->options->existingClones[$this->options->clone]) )
        {
            $this->options->cloneNumber = $this->options->existingClones[$this->options->clone]['number'];  
            $this->options->prefix = $this->getStagingPrefix();
        } else {
            wp_die('Fatal Error: Can not update staging because there is no staging data.');
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
            ABSPATH . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'wps-hide-login'
            );

        // Exclude the existing staging website to prevent duplication.
        if(isset($this->options->existingClones[$this->options->clone]) && strlen( $this->options->existingClones[$this->options->clone]['path'])){
	        $excludedDirectories[] = $this->options->existingClones[$this->options->clone]['path'];
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
     * Check and return prefix of the staging site
     */
    public function getStagingPrefix() {
        // Try to get staging prefix from wp-config.php of staging site
        $this->options->prefix = $this->options->existingClones[$this->options->clone]['prefix'];

        if (empty($this->options->prefix)) {
            // Throw error if wp-config.php is not readable 
            $path = ABSPATH . $this->options->cloneDirectoryName . "/wp-config.php";

            if (false === ($content = @file_get_contents($path))) {
                $this->log("Can not open {$path}. Can't read contents", Logger::TYPE_ERROR);
                $this->returnException("Fatal Error: Can not read {$path} to get correct table prefix. Stopping for security reasons. Deleting this staging site and creating a new one could fix this issue. Otherwise contact us Admin");
                wp_die("Fatal Error: Can not read {$path} to get correct table prefix. Stopping for security reasons. Deleting this staging site and creating a new one could fix this issue. Otherwise contact us Admin");
            } else {
                // Get prefix from wp-config.php
                preg_match("/table_prefix\s*=\s*'(\w*)';/", $content, $matches);


                if (!empty($matches[1])) {
                    $this->options->prefix = $matches[1];
                } else {
                    $this->returnException("Fatal Error: Can not detect prefix from {$path}. Stopping for security reasons. Deleting this staging site and creating a new one could fix this issue.");
                    wp_die("Fatal Error: Can not detect prefix from {$path}. Stopping for security reasons. Deleting this staging site and creating a new one could fix this issue.");
                }
            }
        }

        // Die() if staging prefix is the same as the live prefix
        if ($this->db->prefix == $this->options->prefix) {
            $this->log("Fatal Error: Can not updatte staging site. Prefix. '{$this->options->prefix}' is used for the live site. Stopping for security reasons. Deleting this staging site and creating a new one could fix this issue. Otherwise contact us Admin");
            wp_die("Fatal Error: Can not update staging site. Prefix. '{$this->options->prefix}' is used for the live site. Stopping for security reasons. Deleting this staging site and creating a new one could fix this issue. Otherwise contact us Admin");
        }

        // Else
        //$this->returnException($this->options->prefix);
        //wp_die($this->options->prefix);
        return $this->options->prefix;
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

        if (!method_exists($this, $methodName))
        {
            $this->log("Can't execute job; Job's method {$methodName} is not found");
            throw new JobNotFoundException($methodName);
        }

        // Call the job
        //$this->log("execute job: Job's method {$methodName}");
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