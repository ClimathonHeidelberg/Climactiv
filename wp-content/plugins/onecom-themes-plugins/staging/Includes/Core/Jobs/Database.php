<?php
namespace OneStaging\Core\Jobs;

defined( "WPINC" ) or die(); // No Direct Access

use OneStaging\OneStaging;

/**
 * Class Database
 * @package OneStaging\Core\Jobs
 */
class Database extends JobExecutable
{

    /**
     * @var int
     */
    private $total = 0;

    /**
     * @var \WPDB
     */
    private $db;

    /**
     * Initialize
     */
    public function initialize()
    {
        // Variables
        $this->total                = count($this->options->tables);
        $this->db                   = OneStaging::getInstance()->get("wpdb");
    }

    /**
     * Calculate Total Steps in This Job and Assign It to $this->options->totalSteps
     * @return void
     */
    protected function calculateTotalSteps()
    {
        $this->options->totalSteps  = $this->total;
    }

    /**
     * Execute the Current Step
     * Returns false when over threshold limits are hit or when the job is done, true otherwise
     * @return bool
     */
    protected function execute()
    {
        // Over limits threshold
        if ($this->isOverThreshold())
        {
            // Prepare response and save current progress
            $this->prepareResponse(false, false);
            $this->saveOptions();
            return false;
        }

        // No more steps, finished
        if ($this->options->currentStep > $this->total || !isset($this->options->tables[$this->options->currentStep]))
        {
            $this->prepareResponse(true, false);
            return false;
        }

        // Table is excluded
        if (in_array($this->options->tables[$this->options->currentStep]->name, $this->options->excludedTables))
        {
            $this->prepareResponse();
            return true;
        }

        // Copy table
        if (!$this->copyTable($this->options->tables[$this->options->currentStep]->name))
        {
            // Prepare Response
            $this->prepareResponse(false, false);

            // Not finished
            return true;
        }

        // Prepare Response
        $this->prepareResponse();

        // Not finished
        return true;
    }

    /**
     * Get new prefix for the staging site
     * @return string
     */
    private function getTablePrefix(){

	    if($this->options->isStagingSite === true){
		    $tablePrefix = $this->options->existingLive->prefix;

	    }
	    else{
		    $tablePrefix = $this->options->prefix;
	    }
        // Make sure prefix of staging site is NEVER identical to prefix of live site! 
        if ( $tablePrefix == $this->db->prefix ){
            wp_die('Fatal error 7: The new database table prefix '. $tablePrefix .' would be identical to the table prefix of the live site.');
        }  
        return $tablePrefix;
    }

    /**
     * SQL queries execution time will be separate from PHP Exec. (TODO: check in profiling.)
     * @param string $tableName
     * @return bool
     */
    private function copyTable($tableName)
    {
        $newTableName = $this->getTablePrefix() . str_replace($this->db->prefix, null, $tableName);

        // Drop table if necessary
        $this->dropTable($newTableName);

        // Save current job
        $this->setJob($newTableName);

        // Beginning of the job
        if (!$this->startJob($newTableName, $tableName))
        {
            return true;
        }

        // Copy data
        $this->copyData($newTableName, $tableName);

        // Finis the step
        return $this->finishStep();
    }

    /**
     * Copy data from old table to new table
     * @param string $new
     * @param string $old
     */
    private function copyData($new, $old)
    {
        $rows = $this->options->job->start+$this->settings->queryLimit;
        $this->log(
            "DB Copy: {$old} as {$new} between {$this->options->job->start} to {$rows} records"
        );

        $limitation = '';

        if (0 < (int) $this->settings->queryLimit)
        {
            $limitation = " LIMIT {$this->settings->queryLimit} OFFSET {$this->options->job->start}";
        }

        $this->db->query(
            "INSERT INTO {$new} SELECT * FROM {$old} {$limitation}"
        );

        // Set new offset
        $this->options->job->start += $this->settings->queryLimit;
    }

    /**
     * Set the job
     * @param string $table
     */
    private function setJob($table)
    {
        if (isset($this->options->job->current))
        {
            return;
        }

        $this->options->job->current = $table;
        $this->options->job->start   = 0;
    }

    /**
     * Start Job
     * @param string $new
     * @param string $old
     * @return bool
     */
    private function startJob($new, $old)
    {
        if (0 != $this->options->job->start)
        {
            return true;
        }

        $this->log("DB Copy: Creating table {$new} like {$old}");

        $this->db->query("CREATE TABLE {$new} LIKE {$old}");

        $this->options->job->total = (int) $this->db->get_var("SELECT COUNT(1) FROM {$old}");

        if (0 == $this->options->job->total)
        {
            $this->finishStep();
            return false;
        }

        return true;
    }

    /**
     * Finish the step
     */
    private function finishStep()
    {
        // This job is not finished yet
        if ($this->options->job->total > $this->options->job->start)
        {
            return false;
        }

        // Add it to cloned tables listing
        $this->options->clonedTables[]  = $this->options->tables[$this->options->currentStep];

        // Reset job
        $this->options->job             = new \stdClass();

        return true;
    }

    /**
     * Drop table if necessary
     * @param string $new
     */
    private function dropTable($new)
    {
        $old = $this->db->get_var($this->db->prepare("SHOW TABLES LIKE %s", $new));

        if (!$this->shouldDropTable($new, $old))
        {
            return;
        }

        $this->log("DB Copy: {$new} already exists, dropping it first");
        $this->db->query("DROP TABLE {$new}");
    }

    /**
     * Check if table needs to be dropped
     * @param string $new
     * @param string $old
     * @return bool
     */
    private function shouldDropTable($new, $old)
    {
        return (
            $old === $new &&
            (
                !isset($this->options->job->current) ||
                !isset($this->options->job->start) ||
                0 == $this->options->job->start
            )
        );
    }
}