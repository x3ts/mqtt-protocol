<?php

namespace x3ts\mqtt\tests\messages;

use PHPUnit\Framework\TestCase;
use x3ts\mqtt\protocol\messages\MessageBase;
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

    public function testDecode(): void
    {
        $binMsg = pack('CCnCCCC',
            0b10010000, 6, 0x7795, 0x01, 0x02, 0x00, 0x80
        );
        /** @var SubAck $msg */
        $msg = MessageBase::decode($binMsg);
        self::assertInstanceOf(SubAck::class, $msg);
        self::assertEquals(0x7795, $msg->packetIdentifier);
        self::assertEquals(0x01, $msg->returnCodes[0]);
        self::assertEquals(0x02, $msg->returnCodes[1]);
        self::assertEquals(0x00, $msg->returnCodes[2]);
        self::assertEquals(0x80, $msg->returnCodes[3]);
    }
}
