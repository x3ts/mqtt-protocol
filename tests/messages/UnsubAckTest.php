<?php

namespace x3ts\mqtt\tests\messages;

use PHPUnit\Framework\TestCase;
use x3ts\mqtt\protocol\messages\MessageBase;
use x3ts\mqtt\protocol\messages\UnsubAck;

class UnsubAckTest extends TestCase
{
    public function testUnsubAck(): void
    {
        $msg = UnsubAck::newInstance()->setPacketIdentifier(0x832b);
        self::assertEquals(
            pack('CCCC', 0b10110000, 2, 0x83, 0x2b),
            $msg->encode(),
        );
    }

    public function testDecode(): void
    {
        $binMsg = pack('CCCC', 0b10110000, 2, 0x83, 0x2b);
        $msg = MessageBase::decode($binMsg);
        self::assertInstanceOf(UnsubAck::class, $msg);
    }
}
