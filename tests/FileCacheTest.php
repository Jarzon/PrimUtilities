<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use PrimUtilities\FileCache;

class FileCacheTest extends TestCase
{
    public function testSaveCacheFile()
    {
        define('APP', __DIR__ . '/');

        $cache = new FileCache();

        $cache->saveCacheFile('testFile', 'test');

        $this->assertEquals(true, file_exists($cache->getFileLocation('testFile')));

        unlink($cache->getFileLocation('testFile'));
    }
}