<?php

namespace x3ts\mqtt\tests\messages;

use x3ts\mqtt\protocol\messages\PubRec;
use PHPUnit\Framework\TestCase;

class PubRecTest extends TestCase
{

    public function testPubRec(): void
    {
        $msg = PubRec::newInstance()
            ->setPacketIdentifier(0x37fa);
        $binMsg = pack('CCCC', 0b01010000, 2, 0x37, 0xFA);
        self::assertEquals($binMsg, $msg->encode());
    }
}
