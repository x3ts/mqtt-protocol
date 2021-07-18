<?php

namespace x3ts\mqtt\tests\messages;

use PHPUnit\Framework\TestCase;
use x3ts\mqtt\protocol\messages\PubComp;

class PubCompTest extends TestCase
{
    public function testPubComp(): void
    {
        $msg = PubComp::newInstance()
            ->setPacketIdentifier(0x7269);
        $binMsg = pack('CCCC', 0b01110000, 2, 0x72, 0x69);
        self::assertEquals($binMsg, $msg->encode());
    }
}
