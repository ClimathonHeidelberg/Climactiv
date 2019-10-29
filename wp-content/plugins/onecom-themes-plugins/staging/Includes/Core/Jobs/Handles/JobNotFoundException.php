<?php
namespace OneStaging\Core\Jobs\Handles;

defined( "WPINC" ) or die(); // No Direct Access

/**
 * Class JobNotFoundException
 * @package OneStaging\Core\Jobs\Exceptions
 */
class JobNotFoundException extends \Exception
{
    /**
     * @var string
     */
    protected $message = "Can't execute the job; Job's method %s is not found";

    /**
     * JobNotFoundException constructor.
     * @param string $className
     */
    public function __construct($className = "")
    {
        $this->message = sprintf($this->message, $className);
    }
}