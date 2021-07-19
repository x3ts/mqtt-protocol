<?php

namespace x3ts\mqtt\tests\messages;

use PHPUnit\Framework\TestCase;
use x3ts\mqtt\protocol\messages\MessageBase;
use x3ts\mqtt\protocol\messages\PingResp;

class PingRespTest extends TestCase
{
    public function testPingResp(): void
    {
        self::assertEquals(
            pack('CC', 0b11010000, 0),
            PingResp::newInstance()->encode(),
        );
    }

    public function testDecode(): void
    {
        $binMsg = pack('CC', 0b11010000, 0);
        self::assertInstanceOf(PingResp::class, MessageBase::decode($binMsg));
    }
}
