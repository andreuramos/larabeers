<?php

namespace Larabeers\Utils\Tests;

use Larabeers\Utils\NormalizeString;
use PHPUnit\Framework\TestCase;

class NormalizeStringTest extends TestCase
{
    public function test_empty_string()
    {
        $service = $this->getService();
        $this->assertEquals("",$service->execute(""));
    }

    public function test_acute()
    {
        $this->assertEquals("a", $this->getService()->execute("á"));
    }

    public function test_keeps_spaces_and_removes_capitals()
    {
        $this->assertEquals("s a", $this->getService()->execute("Š ã"));
    }

    private function getService()
    {
        return new NormalizeString();
    }
}
