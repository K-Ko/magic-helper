<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use Helper\Magic;
use PHPUnit\Framework\TestCase;

final class IteratorTest extends TestCase
{
    public function testIterator()
    {
        $magic = new Magic(['k' => 'v']);

        $count = 0;

        foreach ($magic as $key => $value) {
            $this->assertEquals('k', $key);
            $this->assertEquals('v', $value);
            $count++;
        }

        $this->assertEquals(1, $count);
    }
}
