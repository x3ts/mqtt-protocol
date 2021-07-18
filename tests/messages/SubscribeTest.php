<?php

namespace x3ts\mqtt\tests\messages;

use PHPUnit\Framework\TestCase;
use x3ts\mqtt\protocol\constants\QoS;
use x3ts\mqtt\protocol\messages\Subscribe;

class SubscribeTest extends TestCase
{
    public function testNoTopic(): void
    {
        $this->expectError();
        Subscribe::newInstance()->setPacketIdentifier(0x3023)->encode();
    }

    public function testOneTopic(): void
    {
        $msg = Subscribe::newInstance()->setPacketIdentifier(0x3023)
            ->addTopicFilter('test/01', QoS::AT_LEAST_ONCE);
        $binMsg = pack('CCCC', 0b10000010, 12, 0x30, 0x23) .
            pack('CC', 0x00, 0x07) . 'test/01' .
            pack('C', 0b00000001);
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testMultiTopic(): void
    {
        $msg = Subscribe::newInstance()->setPacketIdentifier(0x3023)
            ->addTopicFilter('test/01', QoS::AT_LEAST_ONCE)
            ->addTopicFilter('test/02')
            ->addTopicFilter('test/03', QoS::EXACTLY_ONCE);
        $binMsg = pack('CCCC', 0b10000010, 32, 0x30, 0x23) .
            pack('CC', 0x00, 0x07) . 'test/01' . pack('C', 1) .
            pack('CC', 0x00, 0x07) . 'test/02' . pack('C', 0) .
            pack('CC', 0x00, 0x07) . 'test/03' . pack('C', 2);
        self::assertEquals($binMsg, $msg->encode());
    }
}
