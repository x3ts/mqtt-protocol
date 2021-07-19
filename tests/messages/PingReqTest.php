<?php

namespace x3ts\mqtt\tests\messages;

use PHPUnit\Framework\TestCase;
use x3ts\mqtt\protocol\messages\MessageBase;
use x3ts\mqtt\protocol\messages\PingReq;

class PingReqTest extends TestCase
{
    public function testPingReq(): void
    {
        self::assertEquals(
            pack('CC', 0b11000000, 0),
            PingReq::newInstance()->encode(),
        );
    }

    public function testDecode(): void
    {
        $binMsg = pack('CC', 0b11000000, 0);
        self::assertInstanceOf(PingReq::class, MessageBase::decode($binMsg));
    }
}
