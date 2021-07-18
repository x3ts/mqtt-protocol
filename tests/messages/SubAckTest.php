<?php

namespace x3ts\mqtt\tests\messages;

use PHPUnit\Framework\TestCase;
use x3ts\mqtt\protocol\messages\SubAck;

class SubAckTest extends TestCase
{
    public function testNoTopic()
    {
        $this->expectError();
        SubAck::newInstance()->encode();
    }

    public function testErrorResponse()
    {
        $msg = SubAck::newInstance()
            ->genPacketIdentifier()
            ->addReturnCode(0x80);
        $binMsg = pack('CCnC', 0b10010000, 3, $msg->packetIdentifier, 0x80);
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testMultiResponse(): void
    {
        $msg = SubAck::newInstance()
            ->genPacketIdentifier()
            ->addReturnCode(0x01)
            ->addReturnCode(0x02)
            ->addReturnCode(0x00)
            ->addReturnCode(0x80);
        $binMsg = pack('CCnCCCC',
            0b10010000, 6, $msg->packetIdentifier, 0x01, 0x02, 0x00, 0x80
        );
        self::assertEquals($binMsg, $msg->encode());
    }
}
