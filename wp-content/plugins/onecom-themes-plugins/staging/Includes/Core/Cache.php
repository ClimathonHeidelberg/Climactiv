<?php
namespace OneStaging\Core;

defined( "WPINC" ) or die(); // No Direct Access

use OneStaging\OneStaging;

/**
 * Class Cache
 * @package OneStaging\Includes
 */
class Cache
{
    /**
     * Cache directory (full path)
     * @var string
     */
    private $cacheDir;

    /**
     * Cache file extension
     * @var string
     */
    private $cacheExtension = "cache";

    /**
     * Lifetime of cache files in seconds
     * @var int
     */
    private $lifetime = 2592000; // 30 days

    /**
     * Cache constructor.
     * @param null|int $lifetime
     * @param null|string $cacheDir
     * @param null|string $cacheExtension
     * @throws \Exception
     */
    public function __construct($lifetime = null, $cacheDir = null, $cacheExtension = null)
    {

        // Set lifetime
        $lifetime = (int) $lifetime;
        if ($lifetime > 0)
        {
            $this->lifetime = $lifetime;
        }

        // Set cache directory
        if (!empty($cacheDir) && is_dir($cacheDir))
        {
            $this->cacheDir = $cacheDir;
        }
        // Set default
        else
        {

            $this->cacheDir = \OneStaging\OneStaging::getContentDir();
        }

        // Set cache extension
        if (!empty($cacheExtension))
        {
            $this->cacheExtension = $cacheExtension;
        }

        // If cache directory doesn't exists, create it
        if (!is_dir($this->cacheDir) && !@mkdir($this->cacheDir, 0775, true))
        {
            throw new \Exception("Failed to create cache directory " . $this->cacheDir . '! Make sure folder permission is 755 and owner is correct. Should be www-data or similar.');
        }
    }


    /**
     * Get cache
     * @param string $cacheFileName
     * @param mixed $defaultValue
     * @param null|int $lifetime
     * @return mixed|null
     */
    public function get($cacheFileName, $defaultValue = null, $lifetime = null)
    {
        // Check if file is valid
        if (false === ($cacheFile = $this->isValid($cacheFileName, true, $lifetime)))
        {
            return $defaultValue;
        }

        return @unserialize(file_get_contents($cacheFile));
    }

    /**
     * Saves value to given cache file
     * @param string $cacheFileName
     * @param mixed $value
     * @return bool
     * @throws \Exception
     */
    public function save($cacheFileName, $value)
    {
        $cacheFile = $this->cacheDir . $cacheFileName . '.' . $this->cacheExtension;

        // Attempt to delete cache file if it exists
        if (is_file($cacheFile) && !@unlink($cacheFile))
        {
            throw new \Exception("Can't delete existing cache file");
        }

        // Save it to file
        return (file_put_contents($cacheFile, @serialize($value), LOCK_EX) !== false);
        //return (file_put_contents($cacheFile, @serialize($value)) !== false);
    }

    /**
     * Checks if file is valid or not
     * @param $cacheFileName
     * @param bool $deleteFileIfInvalid
     * @param null|int $lifetime
     * @return string|bool
     * @throws \Exception
     */
    public function isValid($cacheFileName, $deleteFileIfInvalid = false, $lifetime = null)
    {
        // Lifetime
        $lifetime = (int) $lifetime;
        if (-1 > $lifetime || 0 == $lifetime)
        {
            $lifetime = $this->lifetime;
        }

        // Get full path of the given cache file
        $cacheFile = $this->cacheDir . $cacheFileName . '.' . $this->cacheExtension;

        // File doesn't exist
        if (!is_file($cacheFile))
        {
            return false;
        }

        // As long as file exists, don't check lifetime
        if (-1 == $lifetime)
        {
            return $cacheFile;
        }

        // Time is up, file is invalid
        if (time() - filemtime($cacheFile) >= $lifetime)
        {

            // Attempt to delete the file
            if ($deleteFileIfInvalid === true && !@unlink($cacheFile))
            {
                throw new \Exception("Attempting to delete invalid cache file has failed!");
            }

            // No need to delete the file, return
            return false;
        }

        return $cacheFile;
    }

    /**
     * Delete a cache file
     * @param string $cacheFileName
     * @return bool
     * @throws \Exception
     */
    public function delete($cacheFileName)
    {
        if (false !== ($cacheFile = $this->isValid($cacheFileName, true)) && false === @unlink($cacheFile))
        {
            throw new \Exception("Couldn't delete cache: {$cacheFileName}. Full Path: {$cacheFile}");
        }

        return true;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * @return string
     */
    public function getCacheExtension()
    {
        return $this->cacheExtension;
    }
}