<?php

namespace x3ts\mqtt\tests\messages;

use PHPUnit\Framework\TestCase;
use x3ts\mqtt\protocol\constants\QoS;
use x3ts\mqtt\protocol\messages\Connect;
use x3ts\mqtt\protocol\messages\MessageBase;
use x3ts\mqtt\protocol\messages\Will;

class ConnectTest extends TestCase
{
    public function testConnect(): void
    {
        $msg = Connect::newInstance()
            ->setWillMessage('bye')
            ->setWillTopic('test/will')
            ->setWillQoS(QoS::AT_LEAST_ONCE)
            ->setKeepAlive(48)
            ->setClientIdentifier('test-01')
            ->setUsername('test')
            ->setPassword('pwd');
        $binMsg = pack('C', 0b00010000) . //fixed header
            pack('C', 0x2E) . // remain length
            pack('CC', 0x00, 0x04) . 'MQTT' . //protocol name
            pack('C', 0x04) . //protocol level
            // Username,Password,WillRetain,WillQoS,WillFlag,CleanSession,Reserved
            pack('C', 0b11001100) . // connect flags
            pack('CC', 0x00, 0x30) . // keep alive 48
            pack('CC', 0x00, 0x07) . 'test-01' . // client id
            pack('CC', 0x00, 0x09) . 'test/will' . // will topic
            pack('CC', 0x00, 0x03) . 'bye' . // will message
            pack('CC', 0x00, 0x04) . 'test' . // username
            pack('CC', 0x00, 0x03) . 'pwd'; // password
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testSetWill(): void
    {
        $msg = Connect::newInstance()
            ->setWill(new Will(
                topic: 'test/will',
                message: 'bye',
                qos: QoS::EXACTLY_ONCE,
                retain: false,
            ))
            ->setKeepAlive(48)
            ->setClientIdentifier('test-01')
            ->setUsername('test')
            ->setPassword('pwd');
        $binMsg = pack('C', 0b00010000) . //fixed header
            pack('C', 0x2E) . // remain length
            pack('CC', 0x00, 0x04) . 'MQTT' . //protocol name
            pack('C', 0x04) . //protocol level
            // Username,Password,WillRetain,WillQoS,WillFlag,CleanSession,Reserved
            pack('C', 0b11010100) . // connect flags
            pack('CC', 0x00, 0x30) . // keep alive 48
            pack('CC', 0x00, 0x07) . 'test-01' . // client id
            pack('CC', 0x00, 0x09) . 'test/will' . // will topic
            pack('CC', 0x00, 0x03) . 'bye' . // will message
            pack('CC', 0x00, 0x04) . 'test' . // username
            pack('CC', 0x00, 0x03) . 'pwd'; // password
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testDecodeNormal(): void
    {
        $binMsg = pack('C', 0b00010000) . //fixed header
            pack('C', 0x2E) . // remain length
            pack('CC', 0x00, 0x04) . 'MQTT' . //protocol name
            pack('C', 0x04) . //protocol level
            // Username,Password,WillRetain,WillQoS,WillFlag,CleanSession,Reserved
            pack('C', 0b11001100) . // connect flags
            pack('CC', 0x00, 0x30) . // keep alive 48
            pack('CC', 0x00, 0x07) . 'test-01' . // client id
            pack('CC', 0x00, 0x09) . 'test/will' . // will topic
            pack('CC', 0x00, 0x03) . 'bye' . // will message
            pack('CC', 0x00, 0x04) . 'test' . // username
            pack('CC', 0x00, 0x03) . 'pwd'; // password
        /** @var Connect $msg */
        $msg = MessageBase::decode($binMsg);
        self::assertInstanceOf(Connect::class, $msg);
        self::assertEquals('test-01', $msg->clientIdentifier);
        self::assertEquals('test/will', $msg->willTopic);
        self::assertEquals('bye', $msg->willMessage);
        self::assertEquals('test', $msg->username);
        self::assertEquals('pwd', $msg->password);
        self::assertEquals(0b11001100, $msg->getConnectFlags());
    }

    public function testConnectWithoutWill(): void
    {
        $msg = Connect::newInstance()
            ->setKeepAlive(48)
            ->setClientIdentifier('test-01')
            ->setUsername('test')
            ->setPassword('pwd');
        $binMsg = pack('C', 0b00010000) . //fixed header
            pack('C', 0x1E) . // remain length
            pack('CC', 0x00, 0x04) . 'MQTT' . //protocol name
            pack('C', 0x04) . //protocol level
            // Username,Password,WillRetain,WillQoS,WillFlag,CleanSession,Reserved
            pack('C', 0b11000000) . // connect flags
            pack('CC', 0x00, 0x30) . // keep alive 48
            pack('CC', 0x00, 0x07) . 'test-01' . // client id
            pack('CC', 0x00, 0x04) . 'test' . // username
            pack('CC', 0x00, 0x03) . 'pwd'; // password
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testDecodeWithoutWill(): void
    {
        $binMsg = pack('C', 0b00010000) . //fixed header
            pack('C', 0x1E) . // remain length
            pack('CC', 0x00, 0x04) . 'MQTT' . //protocol name
            pack('C', 0x04) . //protocol level
            // Username,Password,WillRetain,WillQoS,WillFlag,CleanSession,Reserved
            pack('C', 0b11000000) . // connect flags
            pack('CC', 0x00, 0x30) . // keep alive 48
            pack('CC', 0x00, 0x07) . 'test-01' . // client id
            pack('CC', 0x00, 0x04) . 'test' . // username
            pack('CC', 0x00, 0x03) . 'pwd'; // password
        /** @var Connect $msg */
        $msg = MessageBase::decode($binMsg);
        self::assertInstanceOf(Connect::class, $msg);
        self::assertEquals(0b11000000, $msg->getConnectFlags());
        self::assertEquals(48, $msg->keepAlive);
        self::assertEquals('test-01', $msg->clientIdentifier);
        self::assertEquals('test', $msg->username);
        self::assertEquals('pwd', $msg->password);
    }

    public function testConnectWithoutPassword(): void
    {
        $msg = Connect::newInstance()
            ->setWillMessage('bye')
            ->setWillTopic('test/will')
            ->setWillQoS(QoS::AT_LEAST_ONCE)
            ->setKeepAlive(48)
            ->setClientIdentifier('test-01')
            ->setUsername('test');
        $binMsg = pack('C', 0b00010000) . //fixed header
            pack('C', 0x29) . // remain length
            pack('CC', 0x00, 0x04) . 'MQTT' . //protocol name
            pack('C', 0x04) . //protocol level
            // Username,Password,WillRetain,WillQoS,WillFlag,CleanSession,Reserved
            pack('C', 0b10001100) . // connect flags
            pack('CC', 0x00, 0x30) . // keep alive 48
            pack('CC', 0x00, 0x07) . 'test-01' . // client id
            pack('CC', 0x00, 0x09) . 'test/will' . // will topic
            pack('CC', 0x00, 0x03) . 'bye' . // will message
            pack('CC', 0x00, 0x04) . 'test';
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testDecodeWithoutPassword(): void
    {
        $binMsg = pack('C', 0b00010000) . //fixed header
            pack('C', 0x29) . // remain length
            pack('CC', 0x00, 0x04) . 'MQTT' . //protocol name
            pack('C', 0x04) . //protocol level
            // Username,Password,WillRetain,WillQoS,WillFlag,CleanSession,Reserved
            pack('C', 0b10001100) . // connect flags
            pack('CC', 0x00, 0x30) . // keep alive 48
            pack('CC', 0x00, 0x07) . 'test-01' . // client id
            pack('CC', 0x00, 0x09) . 'test/will' . // will topic
            pack('CC', 0x00, 0x03) . 'bye' . // will message
            pack('CC', 0x00, 0x04) . 'test';
        /** @var Connect $msg */
        $msg = MessageBase::decode($binMsg);
        self::assertInstanceOf(Connect::class, $msg);
        self::assertEquals(48, $msg->keepAlive);
        self::assertEquals(0b10001100, $msg->connectFlags);
        self::assertEquals(1, $msg->willQoS);
        self::assertEquals('test-01', $msg->clientIdentifier);
        self::assertEquals('test/will', $msg->willTopic);
        self::assertEquals('bye', $msg->willMessage);
        self::assertEquals('test', $msg->username);
    }

    public function testConnectWithoutUsername(): void
    {
        $msg = Connect::newInstance()
            ->setWillMessage('bye')
            ->setWillTopic('test/will')
            ->setWillRetain(true)
            ->setWillQoS(QoS::AT_LEAST_ONCE)
            ->setKeepAlive(48)
            ->setClientIdentifier('test-01')
            ->setPassword('pwd');
        $binMsg = pack('C', 0b00010000) . //fixed header
            pack('C', 0x28) . // remain length
            pack('CC', 0x00, 0x04) . 'MQTT' . //protocol name
            pack('C', 0x04) . //protocol level
            // Username,Password,WillRetain,WillQoS,WillFlag,CleanSession,Reserved
            pack('C', 0b01101100) . // connect flags
            pack('CC', 0x00, 0x30) . // keep alive 48
            pack('CC', 0x00, 0x07) . 'test-01' . // client id
            pack('CC', 0x00, 0x09) . 'test/will' . // will topic
            pack('CC', 0x00, 0x03) . 'bye' . // will message
            pack('CC', 0x00, 0x03) . 'pwd';
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testDecodeWithoutUsername(): void
    {
        $binMsg = pack('C', 0b00010000) . //fixed header
            pack('C', 0x28) . // remain length
            pack('CC', 0x00, 0x04) . 'MQTT' . //protocol name
            pack('C', 0x04) . //protocol level
            // Username,Password,WillRetain,WillQoS,WillFlag,CleanSession,Reserved
            pack('C', 0b01101100) . // connect flags
            pack('CC', 0x00, 0x30) . // keep alive 48
            pack('CC', 0x00, 0x07) . 'test-01' . // client id
            pack('CC', 0x00, 0x09) . 'test/will' . // will topic
            pack('CC', 0x00, 0x03) . 'bye' . // will message
            pack('CC', 0x00, 0x03) . 'pwd';
        /** @var Connect $msg */
        $msg = MessageBase::decode($binMsg);
        self::assertInstanceOf(Connect::class, $msg);
        self::assertEquals(48, $msg->keepAlive);
        self::assertEquals(0b01101100, $msg->connectFlags);
        self::assertEquals(1, $msg->willQoS);
        self::assertTrue($msg->willRetain);
        self::assertEquals('test-01', $msg->clientIdentifier);
        self::assertEquals('test/will', $msg->willTopic);
        self::assertEquals('bye', $msg->willMessage);
        self::assertEquals('pwd', $msg->password);
    }

    public function testConnectDisableWill(): void
    {
        $msg = Connect::newInstance()
            ->setWillMessage('bye')
            ->setWillTopic('test/will')
            ->setWillQoS(QoS::AT_LEAST_ONCE)
            ->setKeepAlive(48)
            ->setClientIdentifier('test-01')
            ->setUsername('test')
            ->setPassword('pwd')
            ->disableWill();
        $binMsg = pack('C', 0b00010000) . //fixed header
            pack('C', 0x1E) . // remain length
            pack('CC', 0x00, 0x04) . 'MQTT' . //protocol name
            pack('C', 0x04) . //protocol level
            // Username,Password,WillRetain,WillQoS,WillFlag,CleanSession,Reserved
            pack('C', 0b11000000) . // connect flags
            pack('CC', 0x00, 0x30) . // keep alive 48
            pack('CC', 0x00, 0x07) . 'test-01' . // client id
            pack('CC', 0x00, 0x04) . 'test' . // username
            pack('CC', 0x00, 0x03) . 'pwd'; // password
        self::assertEquals($binMsg, $msg->encode());
    }

    public function testCleanSession(): void
    {
        $msg = Connect::newInstance()
            ->setKeepAlive(48)
            ->setClientIdentifier('test-01')
            ->setCleanSession(true);
        $binMsg = pack('CCCC', 0b00010000, 19, 0x00, 0x04) . 'MQTT' .
            pack('CC', 0x04, 0b00000010) .
            pack('CC', 0x00, 0x30) .
            pack('CC', 0x00, 0x07) . 'test-01';
        self::assertEquals($binMsg, $msg->encode());
    }
}
