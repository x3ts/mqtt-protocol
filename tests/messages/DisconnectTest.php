<?php

namespace x3ts\mqtt\tests\messages;

use PHPUnit\Framework\TestCase;
use x3ts\mqtt\protocol\messages\Disconnect;

class DisconnectTest extends TestCase
{
    public function testDisconnect(): void
    {
        self::assertEquals(
            pack('CC', 0b11100000, 0),
            Disconnect::newInstance()->encode(),
        );
    }
}
