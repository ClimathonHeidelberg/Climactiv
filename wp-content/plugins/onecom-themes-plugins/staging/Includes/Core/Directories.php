<?php
namespace OneStaging\Core;

defined( "WPINC" ) or die(); // No Direct Access

use OneStaging\OneStaging;
/**
 * Class Directories
 * @package OneStaging\Includes
 */
class Directories
{

    /**
     * @var string
     */
    private $OS;

    /**
     * @var Logger
     */
    private $log;

    /**
     * Directories constructor.
     */
    public function __construct()
    {
	    $info = new Info();

        $this->log          = OneStaging::getInstance()->get("logger");
        $this->OS           = $info->getOS();
    }

    /**
     * Gets size of given directory
     * @param string $path
     * @return int|null
     */
    public function size($path)
    {
        // Basics
        $path       = realpath($path);

        // Invalid path
        if (false === $path)
        {
            return null;
        }
        return $this->sizeWithPHP($path);
    }

    /**
     * Get given directory size using PHP
     * @param string $path
     * @return int
     */
    private function sizeWithPHP($path)
    {
        // Iterator
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
        );

        $totalBytes = 0;

        // Loop & add file size
        foreach ($iterator as $file)
        {
            try
            {
                $totalBytes += $file->getSize();
            }
            // Some invalid symbolic links can cause issues in *nix systems
            catch(\Exception $e)
            {
                $this->log->add("{$file} is a symbolic link or for some reason its size is invalid");
            }
        }

        return $totalBytes;
    }
}