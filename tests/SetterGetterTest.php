<?php

declare(strict_types=1);

namespace Tests;

use Helper\Magic;
use PHPUnit\Framework\TestCase;

final class SetterGetterTest extends TestCase
{
    public function testArray()
    {
        $magic = new Magic();

        $magic['k'] = 'v';

        $this->check($magic);

        $this->assertTrue(isset($magic['k']));
        unset($magic['k']);
        $this->assertFalse(isset($magic['k']));
    }

    public function testObject()
    {
        $magic = new Magic();

        $magic->k = 'v';

        $this->check($magic);

        $this->assertTrue(isset($magic->k));
        unset($magic->k);
        $this->assertFalse(isset($magic->k));
    }

    public function testSetter()
    {
        $magic = new Magic();

        $magic->set('k', 'v');

        $this->check($magic);

        $this->assertTrue($magic->has('k'));
        $magic->delete('k');
        $this->assertFalse($magic->has('k'));
    }

    public function testUnset()
    {
        $magic = new Magic(['k' => 'v']);

        $this->assertEquals(1, count($magic));
        $this->assertTrue($magic->has('k'));

        unset($magic['k']);

        $this->assertEquals(0, count($magic));
        $this->assertFalse($magic->has('k'));
        $this->assertNull($magic['k']);
    }

    public function testExistsDeleteMulti()
    {
        $magic = new Magic(['k1' => 'v1', 'k2' => 'v2', 'k3' => 'v3']);

        $this->assertEquals(3, count($magic));
        $this->assertTrue($magic->has('k1', 'k2', 'k3'));
        $this->assertFalse($magic->has('k1', 'k2', 'k3', 'k4'));

        $magic->delete('k1');

        $this->assertEquals(2, count($magic));

        $magic->delete('k2', 'k3');

        $this->assertEquals(0, count($magic));
    }

    public function testSetterGetterByMagicCall()
    {
        $magic = new Magic();

        $magic->setCompoundKey('v');

        $this->assertTrue($magic->has('compound_key'));
        $this->assertEquals('v', $magic->getCompoundKey());

        $this->assertNull($magic->getInvalidKey());
    }

    public function testSetterIfEmpty()
    {
        $magic = new Magic();

        $magic->set('k', 'v');
        $magic->setIfEmpty('k', 'w');
        $this->assertEquals('v', $magic->get('k'));

        $this->assertNull($magic->get('l'));
        $magic->setIfEmpty('l', 'm');
        $this->assertEquals('m', $magic->get('l'));
    }

    public function testSetterReservedNames()
    {
        $magic = new Magic();
        $magic->withCallables = false;

        $magic->set('name', 'Max');
        $this->assertEquals('Max', $magic->get('name'));
    }

    private function check(Magic $magic)
    {
        $this->assertEquals(1, count($magic));
        $this->assertTrue($magic->has('k'));
        $this->assertEquals('v', $magic['k']);
        $this->assertEquals('v', $magic->k);
        $this->assertEquals('v', $magic->get('k'));
    }
}
