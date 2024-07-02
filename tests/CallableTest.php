<?php

declare(strict_types=1);

namespace Tests;

use Helper\Magic;
use PHPUnit\Framework\TestCase;

// phpcs:disable PSR1.Files.SideEffects.FoundWithSymbols
// https://github.com/oittaa/uuid-php/
$UUID4 = static function (): string {

    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
};
// phpcs:enable

final class CallableTest extends TestCase
{
    public function testCallable()
    {
        global $UUID4;

        $magic = new Magic();

        $magic->set('pid', $UUID4);

        $this->assertEquals($magic->get('pid'), $magic->get('pid'));
    }

    public function testProtect()
    {
        global $UUID4;

        $magic = new Magic();

        $magic->protect('uuid', $UUID4);

        $this->assertNotEquals(call_user_func($magic->get('uuid')), call_user_func($magic->get('uuid')));
    }
}
