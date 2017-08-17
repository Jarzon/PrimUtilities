<?php
namespace PrimUtilities;

class FileCache
{

    public function cacheTimestamp(string $file) : int
    {
        $location = $this->getFileLocation($file);

        if(file_exists($location)) {
            return filemtime($location);
        }

        return 0;
    }

    public function saveCacheFile(string $file, $data)
    {
        $location = $this->getFileLocation($file);

        return file_put_contents($location, serialize($data));
    }

    public function getCacheFile(string $file)
    {
        $location = $this->getFileLocation($file);

        if(file_exists($location)) {
            return unserialize(file_get_contents($location));
        }

        return false;
    }

    public function getFileLocation(string $file) : string
    {
        return APP . "cache/$file";
    }
}