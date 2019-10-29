<?php
namespace OneStaging\Core\Jobs;

defined( "WPINC" ) or die(); // No Direct Access

/**
 * Class JobExecutable
 * Mainly for supporting PHP all around
 * @package OneStaging\Core\Jobs
 */
abstract class JobExecutable extends Job
{

    /**
     * @var array
     */
    protected $response = array(
        "status"        => false,
        "percentage"    => 0,
        "total"         => 0,
        "step"          => 0,
        "last_msg"      => '',
    );

    /**
     * JobExecutable constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Calculate total steps
        $this->calculateTotalSteps();
    }

    /**
     * Prepare Response Array
     * @param bool $status
     * @param bool $incrementCurrentStep
     * @return array
     */
    protected function prepareResponse($status = false, $incrementCurrentStep = true)
    {
        if ($incrementCurrentStep)
        {
            $this->options->currentStep++;
        }

        $percentage = round(($this->options->currentStep / $this->options->totalSteps) * 100);
        $percentage = (100 < $percentage) ? 100 : $percentage;

        return $this->response = array(
            "status"        => $status,
            "percentage"    => $percentage,
            "total"         => $this->options->totalSteps,
            "step"          => $this->options->currentStep,
            "job"           => $this->options->currentJob,
            "last_msg"      => $this->logger->getLastLogMsg(),
            "running_time"  => $this->time() - time(),
            "job_done"      => $status
        );
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

        return (object) $this->response;
    }
    
    /**
     * Run Steps
     */
    protected function run()
    {
        // Execute steps
        for ($i = 0; $i < $this->options->totalSteps; $i++)
        {
            // Job is finished or over threshold limits was hit
            if (!$this->execute())
            {
                break;
            }
        }
    }

    /**
     * Calculate Total Steps in This Job and Assign It to $this->options->totalSteps
     * @return void
     */
    abstract protected function calculateTotalSteps();

    /**
     * Execute the Current Step
     * Returns false when over threshold limits are hit or when the job is done, true otherwise
     * @return bool
     */
    abstract protected function execute();
}