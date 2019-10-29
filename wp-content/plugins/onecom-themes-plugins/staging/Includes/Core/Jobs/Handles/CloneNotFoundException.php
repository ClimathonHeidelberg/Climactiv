<?php
namespace OneStaging\Core\Jobs\Handles;

defined( "WPINC" ) or die(); // No Direct Access

/**
 * Class CloneNotFoundException
 * @package OneStaging\Core\Jobs\Exceptions
 */
class CloneNotFoundException extends \Exception
{
    /**
     * @var string
     */
    protected $message = "Clone name is not set or clone not found";
}