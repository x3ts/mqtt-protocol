<?php

namespace x3ts\mqtt\tests\messages;

use x3ts\mqtt\protocol\messages\PubAck;
use PHPUnit\Framework\TestCase;

class PubAckTest extends TestCase
{
    public function testPubAck(): void
    {
        $msg = PubAck::newInstance()
            ->setPacketIdentifier(0x10A7);
        $binMsg = pack('CCCC', 0b01000000, 2, 0x10, 0xA7);
        self::assertEquals($binMsg, $msg->encode());
    }
}
