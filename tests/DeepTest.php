<?php

declare(strict_types=1);

namespace Tests;

use Helper\Magic;
use PHPUnit\Framework\TestCase;

final class DeepTest extends TestCase
{
    public function testCreateDeepFromArray()
    {
        $this->check(new Magic(['k' => 'v', 'a' => ['k' => 'v']]));
    }

    public function testCreateDeepFromJson()
    {
        $this->check(Magic::fromJSON('{"k":"v","a":{"k":"v"}}'));
    }

    public function testCreateDeepFromYaml()
    {
        $this->check(Magic::fromYAML('k: v' . PHP_EOL . 'a:' . PHP_EOL . '    k: v'));
    }

    public function testToArray()
    {
        $data = ['k' => 'v', 'a' => ['k' => 'v']];

        $magic = new Magic($data);

        // toArray() sorts by key!
        $sorted = ['a' => ['k' => 'v'], 'k' => 'v'];

        $this->assertSame($sorted, $magic->toArray());
    }

    private function check(Magic $magic)
    {
        $this->assertEquals(2, count($magic));
        $this->assertTrue($magic->exists('k'));
        $this->assertEquals('v', $magic['k']);
        $this->assertEquals('v', $magic->k);
        $this->assertEquals('v', $magic->get('k'));

        $this->assertInstanceOf(Magic::class, $magic['a']);

        $this->assertTrue($magic['a']->exists('k'));
        $this->assertTrue($magic->a->exists('k'));
        $this->assertTrue($magic->get('a')->exists('k'));

        $this->assertEquals('v', $magic['a']['k']);
        $this->assertEquals('v', $magic['a']->k);
        $this->assertEquals('v', $magic['a']->get('k'));
    }
}
