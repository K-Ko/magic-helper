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

        $this->assertTrue($magic->exists('k'));
        $magic->delete('k');
        $this->assertFalse($magic->exists('k'));
    }

    public function testUnset()
    {
        $magic = new Magic(['k' => 'v']);

        $this->assertEquals(1, count($magic));
        $this->assertTrue($magic->exists('k'));

        unset($magic['k']);

        $this->assertEquals(0, count($magic));
        $this->assertFalse($magic->exists('k'));
        $this->assertNull($magic['k']);
    }

    public function testSetterGetterByMagicCall()
    {
        $magic = new Magic();

        $magic->setCompoundKey('v');

        $this->assertTrue($magic->exists('compound_key'));
        $this->assertEquals('v', $magic->getCompoundKey());

        $this->assertNull($magic->getInvalidKey());
    }

    private function check(Magic $magic)
    {
        $this->assertEquals(1, count($magic));
        $this->assertTrue($magic->exists('k'));
        $this->assertEquals('v', $magic['k']);
        $this->assertEquals('v', $magic->k);
        $this->assertEquals('v', $magic->get('k'));
    }
}
