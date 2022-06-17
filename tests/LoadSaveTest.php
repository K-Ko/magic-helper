<?php

declare(strict_types=1);

namespace Tests;

use Helper\Magic;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class LoadSaveTest extends TestCase
{
    public static $filename;

    public static function setUpBeforeClass(): void
    {
        self::$filename = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test.magic.ser';
    }

    public static function tearDownAfterClass(): void
    {
        unlink(self::$filename);
    }

    public function testSave()
    {
        $magic = new Magic(['k' => 'v']);

        $rc = $magic->save(self::$filename);

        $this->assertGreaterThan(0, $rc);
    }

    /**
     * @depends testSave
     */
    public function testLoad()
    {
        $magic = Magic::fromFile(self::$filename);

        $this->assertEquals(1, count($magic));
        $this->assertTrue($magic->exists('k'));
        $this->assertEquals('v', $magic['k']);
        $this->assertEquals('v', $magic->k);
        $this->assertEquals('v', $magic->get('k'));
    }

    public function testLoadInvalid()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(2);

        Magic::fromFile(__DIR__ . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'invalid.ser');
    }

    public function testLoadMissing()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1);

        Magic::fromFile(__DIR__ . DIRECTORY_SEPARATOR . 'missing.ser');
    }
}
