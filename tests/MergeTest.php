<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use Helper\Magic;
use PHPUnit\Framework\TestCase;

final class MergeTest extends TestCase
{
    public function testMerge()
    {
        $magic = new Magic(['k' => ['k' => 'v']]);

        $magic->merge('k', ['x' => 'y']);

        $this->assertEquals(2, count($magic['k']));
    }

    public function testMergeInvalid()
    {
        $magic = new Magic(['k' => 'v']);

        $this->expectException(Exception::class);

        $magic->merge('k', ['x' => 'y']);
    }
}
