<?php

namespace x3ts\mqtt\tests\messages;

use x3ts\mqtt\protocol\constants\QoS;
use x3ts\mqtt\protocol\messages\MessageBase;
use x3ts\mqtt\protocol\messages\Publish;
use PHPUnit\Framework\TestCase;

class PublishTest extends TestCase
{
    private Publish $msg;

    private int $packetId;

    public function setUp(): void
    {
        $this->msg = Publish::newInstance()->genPacketIdentifier();
        $this->packetId = $this->msg->packetIdentifier;
    }

    public function testSetQoS()
    {
        $msg = $this->msg->setQoS(QoS::AT_LEAST_ONCE)
            ->setTopic('test');
        $binMsg = pack('CCCC', 0b00110010, 8, 0x00, 0x04) . 'test' .
            pack('n', $this->packetId);
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testSetPayload()
    {
        $msg = $this->msg->setTopic('test')->setPayload('hello');
        $binMsg = pack('CCCC', 0b00110000, 11, 0x00, 0x04) . 'test' . 'hello';
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testSetDupWhenQoS0(): void
    {
        $msg = $this->msg->setTopic('test')->setDup(true)->setPayload('abc');
        $binMsg = pack('CCCC', 0b00110000, 9, 0x00, 0x04) . 'test' . 'abc';
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testSetDupWhenQoS1(): void
    {
        $msg = $this->msg->setTopic('test')->setDup(true)->setQoS(QoS::AT_LEAST_ONCE);
        $binMsg = pack('CCCC', 0b00111010, 8, 0x00, 0x04) . 'test' .
            pack('n', $this->packetId);
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testDecodeDup(): void
    {
        $binMsg = pack('CCCC', 0b00111010, 8, 0x00, 0x04) . 'test' .
            pack('n', $this->packetId);
        /** @var Publish $msg */
        $msg = MessageBase::decode($binMsg);
        self::assertInstanceOf(Publish::class, $msg);
        self::assertEquals('test', $msg->topic);
        self::assertEquals(1, $msg->qos);
        self::assertTrue($msg->dup);
    }

    public function testEmptyTopic(): void
    {
        $this->expectError();
        $this->msg->setTopic('')->encode();
    }

    public function testSetRetain(): void
    {
        $msg = $this->msg->setTopic('test')->setRetain(true);
        $binMsg = pack('CCCC', 0b00110001, 6, 0x00, 0x04) . 'test';
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testDecodeRetain(): void
    {
        $binMsg = pack('CCCC', 0b00110001, 6, 0x00, 0x04) . 'test';
        /** @var Publish $msg */
        $msg = MessageBase::decode($binMsg);
        self::assertInstanceOf(Publish::class, $msg);
        self::assertTrue($msg->retain);
        self::assertEquals('test', $msg->topic);
    }

    public function testLongPayload(): void
    {
        $payload = 'If  the  Keep  Alive  value  is  non-zero  and  the  Server  does  not  receive  a  Control  Packet' .
            '  from  the  Client 538 within one and a half times the Keep Alive time period, it MUST disconnect the' .
            ' Network Connection to the 539 Client as if the network had failed. If a Client  does  not receive a' .
            ' PINGRESP Packet within a reasonable amount of  time after  it has sent a 542 PINGREQ, it SHOULD close the' .
            ' Network Connection to the Server';
        $msg = $this->msg->setTopic('test')
            ->setPayload($payload);
        $binMsg = pack('CCCCC', 0b00110000, 0xbe, 0x03, 0x00, 0x04) . 'test' . $payload;
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testDecodeLongPayload(): void
    {
        $payload = 'If  the  Keep  Alive  value  is  non-zero  and  the  Server  does  not  receive  a  Control  Packet' .
            '  from  the  Client 538 within one and a half times the Keep Alive time period, it MUST disconnect the' .
            ' Network Connection to the 539 Client as if the network had failed. If a Client  does  not receive a' .
            ' PINGRESP Packet within a reasonable amount of  time after  it has sent a 542 PINGREQ, it SHOULD close the' .
            ' Network Connection to the Server';
        $binMsg = pack('CCCCC', 0b00110000, 0xbe, 0x03, 0x00, 0x04) . 'test' . $payload;
        /** @var Publish $msg */
        $msg = MessageBase::decode($binMsg);
        self::assertInstanceOf(Publish::class, $msg);
        self::assertEquals('test', $msg->topic);
        self::assertEquals(0, $msg->qos);
        self::assertFalse($msg->retain);
        self::assertFalse($msg->dup);
    }
}
