<?php
namespace OneStaging\Core\Jobs;

defined( "WPINC" ) or die(); // No Direct Access

use OneStaging\OneStaging;
use OneStaging\Core\Logger;
use OneStaging\Core\Iterators\RecursiveDirectoryIterator;
use OneStaging\Core\Iterators\RecursiveFilterNewLine;
use OneStaging\Core\Iterators\RecursiveFilterExclude;

/**
 * Class Files
 * @package OneStaging\Core\Directories
 */
class Directories extends JobExecutable {

    /**
     * @var array
     */
    private $files = array();

    /**
     * Total steps to do
     * @var int
     */
    private $total = 4;
    private $fileHandle;

    private $filename;

    /**
     * Initialize
     */
    public function initialize() {
        $this->filename = $this->cache->getCacheDir() . "files_to_copy." . $this->cache->getCacheExtension();
    }

    /**
     * Calculate Total Steps in This Job and Assign It to $this->options->totalSteps
     * @return void
     */
    protected function calculateTotalSteps() {
        $this->options->totalSteps = $this->total;
    }

    /**
     * Start Module
     * @return object
     */
    public function start() {

        // Execute steps
        $this->run();

        // Save option, progress
        $this->saveProgress();

        return (object) $this->response;
    }

    /**
     * Step 1
     * Get WP Root files
     */
    private function getWpRootFiles() {

        // open file handle
        $files = $this->open($this->filename, 'a');


        try {
            // Iterate over wp root directory
            $iterator = new \DirectoryIterator(ABSPATH);

            // Write path line
            foreach ($iterator as $item) {
                if (!$item->isDot() && $item->isFile() && $this->isRequiredFile($item->getFilename())) {
                    if ($this->write($files, $iterator->getFilename() . PHP_EOL)) {
                        $this->options->totalFiles++;

                        // Add current file size
                        $this->options->totalFileSize += $iterator->getSize();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->returnException('Error: ' . $e->getMessage());
            //throw new \Exception('Out of disk space.');
        } catch (\Exception $e) {
            // Skip bad file permissions
        }

        $this->close($files);
        return true;

    }

    /**
     * Step 2
     * Get WP Content Files
     */
    public function getWpContentFiles() {

//        if (1 < $this->options->totalFiles) {
//            return;
//        }

        // open file handle
        $files = $this->open($this->filename, 'a');

        $excludeWpContent = array(
            'HUGE-FOLDER',
            'cache',
            'wps-hide-login',
            'wp-clone',
            'node_modules',
            'nbproject',
            '.git',
            '.idea'
        );

        try {

            // Iterate over content directory
            $iterator = new \OneStaging\Core\Iterators\RecursiveDirectoryIterator(WP_CONTENT_DIR);

            // Exclude new line file names [not using this as it results into 500 in some cases]
            //$iterator = new \OneStaging\Core\Iterators\RecursiveFilterNewLine($iterator);

            // Exclude uploads, plugins or themes
            $iterator = new \OneStaging\Core\Iterators\RecursiveFilterExclude($iterator, apply_filters('wpstg_exclude_content', $excludeWpContent));
            // Recursively iterate over content directory
            $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::LEAVES_ONLY, \RecursiveIteratorIterator::CATCH_GET_CHILD);

            // Write path line
            foreach ($iterator as $item) {
                if ($item->isFile()) {
                    if ($this->write($files, 'wp-content' . DIRECTORY_SEPARATOR . $iterator->getSubPathName() . PHP_EOL)) {
                        $this->options->totalFiles++;

                        // Add current file size
                        $this->options->totalFileSize += $iterator->getSize();
            }
        }
            }
        } catch (\Exception $e) {
            //$this->returnException('Out of disk space.');
            throw new \Exception('Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Skip bad file permissions
        }

        // close the file handler
        $this->close($files);
        return true;
    }

    /**
     * Step 3
     * @return boolean
     * @throws \Exception
     */
    public function getWpIncludesFiles() {

        // open file handle and attach data to end of file
        $files = $this->open($this->filename, 'a');

        try {

            // Iterate over wp-admin directory
            $iterator = new \OneStaging\Core\Iterators\RecursiveDirectoryIterator(ABSPATH . 'wp-includes' . DIRECTORY_SEPARATOR);

            // Exclude new line file names
            //$iterator = new \OneStaging\Core\Iterators\RecursiveFilterNewLine($iterator);

            // Exclude uploads, plugins or themes
            //$iterator = new \OneStaging\Iterators\RecursiveFilterExclude($iterator, apply_filters('wpstg_exclude_content', $exclude_filters));
            // Recursively iterate over wp-includes directory
            $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::LEAVES_ONLY, \RecursiveIteratorIterator::CATCH_GET_CHILD);

            // Write files
            foreach ($iterator as $item) {
                if ($item->isFile()) {
                    if ($this->write($files, 'wp-includes' . DIRECTORY_SEPARATOR . $iterator->getSubPathName() . PHP_EOL)) {
               $this->options->totalFiles++;

                        // Add current file size
                        $this->options->totalFileSize += $iterator->getSize();
            }
            }
            }
        } catch (\Exception $e) {
            //$this->returnException('Out of disk space.');
            throw new \Exception('Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Skip bad file permissions
        }

        // close the file handler
        $this->close($files);
        return true;
            }

            /**
     * Step 4
     * @return boolean
     * @throws \Exception
             */
    public function getWpAdminFiles() {

        // open file handle and attach data to end of file
        $files = $this->open($this->filename, 'a');

        try {

            // Iterate over wp-admin directory
            $iterator = new \OneStaging\Core\Iterators\RecursiveDirectoryIterator(ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR);

            // Exclude new line file names
            //$iterator = new \OneStaging\Core\Iterators\RecursiveFilterNewLine($iterator);

            // Exclude uploads, plugins or themes
            //$iterator = new \OneStaging\Iterators\RecursiveFilterExclude($iterator, apply_filters('wpstg_exclude_content', $exclude_filters));
            // Recursively iterate over content directory
            $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::LEAVES_ONLY, \RecursiveIteratorIterator::CATCH_GET_CHILD);

            // Write path line
            foreach ($iterator as $item) {
                if ($item->isFile()) {
                    if ($this->write($files, 'wp-admin' . DIRECTORY_SEPARATOR . $iterator->getSubPathName() . PHP_EOL)) {
                        $this->options->totalFiles++;
                        // Add current file size
                        $this->options->totalFileSize += $iterator->getSize();
    }
            }
        }
        } catch (\Exception $e) {
            //$this->returnException('Out of disk space.');
            throw new \Exception('Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Skip bad file permissions
        }

        // close the file handler
        $this->close($files);
        return true;
    }

	/**
	 * @param $filename
	 * @return true/false
	 * */
    public function isRequiredFile($file){
    	// Allow .PHP, .HTML, .TXT files only.
	    return preg_match('/^.*\.(php|txt|html)$/i', $file);
	}

	/**
     * Closes a file handle
     *
     * @param  resource $handle File handle to close
     * @return boolean
     */
    public function close($handle) {
        return @fclose($handle);
            }

    /**
     * Opens a file in specified mode
     *
     * @param  string   $file Path to the file to opend
     * @param  string   $mode Mode in which to open the file
     * @return resource
     * @throws Exception
     */
    public function open($file, $mode) {

        $file_handle = @fopen($file, $mode);
        if (false === $file_handle) {
            $this->returnException(sprintf(__('Unable to open %s with mode %s', 'onecom-wp'), $file, $mode));
        }

        return $file_handle;
    }

    /**
     * Write contents to a file
     *
     * @param  resource $handle  File handle to write to
     * @param  string   $content Contents to write to the file
     * @return integer
     * @throws Exception
     */
    public function write($handle, $content) {
        $write_result = @fwrite($handle, $content);
        if (false === $write_result) {
            if (( $meta = \stream_get_meta_data($handle))) {
                //$this->returnException(sprintf(__('Unable to write to: %s', 'onecom-wp'), $meta['uri']));
                throw new \Exception(sprintf(__('Unable to write to: %s', 'onecom-wp'), $meta['uri']));
        }
        } elseif (strlen($content) !== $write_result) {
            //$this->returnException(__('Out of disk space.', 'onecom-wp'));
            throw new \Exception(__('Out of disk space.', 'onecom-wp'));
        }

        return $write_result;
    }

    /**
     * Execute the Current Step
     * Returns false when over threshold limits are hit or when the job is done, true otherwise
     * @return bool
     */
    protected function execute() {
        // No job left to execute
        if( $this->isFinished() ) {
            $this->prepareResponse( true, false );
            return false;
        }


        if ($this->options->currentStep == 0) {
            $this->getWpRootFiles();
            $this->prepareResponse(false, true);
            return false;
        }

        if ($this->options->currentStep == 1) {
            $this->getWpContentFiles();
            $this->prepareResponse(false, true);
            return false;
        }

        if ($this->options->currentStep == 2) {
            $this->getWpIncludesFiles();
            $this->prepareResponse(false, true);
            return false;
        }

        if ($this->options->currentStep == 3) {
            $this->getWpAdminFiles();
            $this->prepareResponse(false, true);
            return false;
        }


        // Prepare response
        $this->prepareResponse(false, true);
        // Not finished
        return true;
    }

    /**
     * Checks Whether There is Any Job to Execute or Not
     * @return bool
     */
    protected function isFinished() {
        return (
                $this->options->currentStep > $this->total ||
                $this->options->currentStep == 4
                );
    }

    /**
     * Save files
     * @return bool
     */
    protected function saveProgress() {
        return $this->saveOptions();
    }

    /**
     * Get files
     * @return void
    */
    protected function getFiles() {
        $fileName = $this->cache->getCacheDir() . "files_to_copy." . $this->cache->getCacheExtension();

        if (false === ($this->files = @file_get_contents($fileName))) {
            $this->files = array();
            return;
         }

        $this->files = explode(PHP_EOL, $this->files);
      }
}
