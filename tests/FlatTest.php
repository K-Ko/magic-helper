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

    public function testCreateFlatFromJsonWithComments()
    {
        $this->check(Magic::fromJSONwithComments('
        {
            // Comment
            "k": "v"
        }
        '));
    }

    public function testCreateFlatFromJsonWithComment()
    {
        $this->check(Magic::fromJSONwithComments('{"k": "NO // comment!"}'), 'NO // comment!');
    }

    public function testCreateFlatFromYaml()
    {
        $this->check(Magic::fromYAML('k: v'));
    }

    public function testCreateFlatFromString()
    {
        $this->check(Magic::fromString('k=v'));
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

    public function testNotExists()
    {
        $magic = new Magic();

        $this->assertEquals(0, count($magic));
        $this->assertFalse($magic->has('x'));
        $this->assertNull($magic['x']);
        $this->assertNull($magic->x);
        $this->assertNull($magic->get('x'));
        $this->assertEquals('y', $magic->get('x', 'y'));
    }

    public function testSort()
    {
        $magic = new Magic(['z' => 'z']);
        $magic['a'] = 'a';

        $this->assertSame((new Magic($magic->toArray(true)))->toArray(), $magic->toArray(true));
    }

    public function testSortAuto()
    {
        $magic = new Magic(['z' => 'z']);
        $magic['a'] = 'a';

        $this->assertSame(['a' => 'a', 'z' => 'z'], $magic->toArray(true));
    }

    private function check(Magic $magic, string $value = 'v')
    {
        $this->assertEquals(1, count($magic));
        $this->assertTrue($magic->has('k'));
        $this->assertEquals($value, $magic['k']);
        $this->assertEquals($value, $magic->k);
        $this->assertEquals($value, $magic->get('k'));

        $magic->clear();
        $this->assertEquals(0, count($magic));
    }
}
