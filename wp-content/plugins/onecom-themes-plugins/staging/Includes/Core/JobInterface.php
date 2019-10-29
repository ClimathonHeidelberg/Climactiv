<?php
namespace OneStaging\Core;

defined( "WPINC" ) or die(); // No Direct Access

/**
 * Interface JobInterface
 * @package OneStaging\Core\Jobs\Interfaces
 */
interface JobInterface
{
    /**
     * Start Module
     * @return object
     */
    public function start();
}