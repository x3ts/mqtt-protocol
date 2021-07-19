<?php

namespace x3ts\mqtt\tests\messages;

use PHPUnit\Framework\TestCase;
use x3ts\mqtt\protocol\messages\MessageBase;
use x3ts\mqtt\protocol\messages\Unsubscribe;

class UnsubscribeTest extends TestCase
{
    public function testUnsubscribeNoTopic(): void
    {
        $this->expectError();
        Unsubscribe::newInstance()->encode();
    }

    public function testUnsubscribeMultiTopics(): void
    {
        $msg = Unsubscribe::newInstance()->genPacketIdentifier()
            ->addTopic('test/01')
            ->addTopic('test/02');
        $binMsg = pack('CCn', 0b10100010, 20, $msg->packetIdentifier) .
            pack('CC', 0x00, 0x07) . 'test/01' .
            pack('CC', 0x00, 0x07) . 'test/02';
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testDecode(): void
    {
        $binMsg = pack('CCn', 0b10100010, 20, 0x9823) .
            pack('CC', 0x00, 0x07) . 'test/01' .
            pack('CC', 0x00, 0x07) . 'test/02';
        /** @var Unsubscribe $msg */
        $msg = MessageBase::decode($binMsg);
        self::assertInstanceOf(Unsubscribe::class, $msg);
        self::assertEquals('test/01', $msg->topics[0]);
        self::assertEquals('test/02', $msg->topics[1]);
    }
}
