<?php

declare(strict_types=1);

namespace Tests;

use BadMethodCallException;
use Helper\Magic;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class InvalidTest extends TestCase
{
    public function testCreateFlatFromInvalidJson()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(100);

        Magic::fromJSON('{"k""v"}');
    }

    public function testCreateFlatFromInvalidYaml()
    {
        $this->expectException(InvalidArgumentException::class);

        Magic::fromYAML('> a');
    }

    public function testCreateFlatFromInvalidIni()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(103);

        Magic::fromINI('a == a');
    }

    public function testInvalidMethod()
    {
        $this->expectException(BadMethodCallException::class);

        $magic = Magic::fromJSON('{"k":"v"}');

        $magic->invalidMethod();
    }
}
