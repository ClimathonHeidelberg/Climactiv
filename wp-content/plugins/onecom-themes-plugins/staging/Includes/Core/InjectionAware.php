<?php
namespace OneStaging\Core;

defined( "WPINC" ) or die(); // No Direct Access

use OneStaging\OneStaging;
/**
 * Class InjectionAware
 * @package OneStaging\DI
 */
abstract class InjectionAware
{

    /**
     * @var OneStaging
     */
    protected $di;

    /**
     * InjectionAware constructor.
     * @param $di
     */
    public function __construct($di)
    {
        $this->di = $di;

        if (method_exists($this, "initialize"))
        {
            $this->initialize();
        }
    }

    /**
     * @return OneStaging
     */
    public function getDI()
    {
        return $this->di;
    }
}