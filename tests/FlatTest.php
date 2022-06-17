<?php

declare(strict_types=1);

namespace Tests;

use Helper\Magic;
use PHPUnit\Framework\TestCase;

final class FlatTest extends TestCase
{
    public function testCreateFlatFromArray()
    {
        $this->check(new Magic(['k' => 'v']));
    }

    public function testCreateFlatFromJson()
    {
        $this->check(Magic::fromJSON('{"k":"v"}'));
    }

    public function testCreateFlatFromYaml()
    {
        $this->check(Magic::fromYAML('k: v'));
    }

    public function testCreateFlatFromIni()
    {
        $this->check(Magic::fromINI('k = v'));
    }

    public function testSerialize()
    {
        $magic = new Magic(['k' => 'v']);

        $this->assertEquals('{"k":"v"}', json_encode($magic));
        $this->assertEquals('{"k":"v"}', (string) $magic);
    }

    public function testMerge()
    {
        $magic = new Magic(['k' => ['k' => 'v']]);

        $magic->merge('k', ['x' => 'y']);

        $this->assertEquals(2, count($magic['k']));
    }

    public function testNotExists()
    {
        $magic = new Magic();

        $this->assertEquals(0, count($magic));
        $this->assertFalse($magic->exists('x'));
        $this->assertNull($magic['x']);
        $this->assertNull($magic->x);
        $this->assertNull($magic->get('x'));
        $this->assertEquals('y', $magic->get('x', 'y'));
    }

    private function check(Magic $magic)
    {
        $this->assertEquals(1, count($magic));
        $this->assertTrue($magic->exists('k'));
        $this->assertEquals('v', $magic['k']);
        $this->assertEquals('v', $magic->k);
        $this->assertEquals('v', $magic->get('k'));

        $magic->clear();
        $this->assertEquals(0, count($magic));
    }
}
