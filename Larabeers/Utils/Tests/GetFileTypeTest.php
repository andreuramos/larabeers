<?php

namespace Larabeers\Utils\Tests;

use Larabeers\Utils\GetFileType;
use PHPUnit\Framework\TestCase;

class GetFileTypeTest extends TestCase
{
    public function test_jpeg()
    {
        $file = __DIR__ . '/gopnik.jpg';
        $type = $this->getService()->execute($file);

        $this->assertEquals('image/jpeg', $type);
    }

    private function getService()
    {
        return new GetFileType();
    }
}
