<?php

namespace x3ts\mqtt\tests\messages;

use x3ts\mqtt\protocol\messages\PubRel;
use PHPUnit\Framework\TestCase;

class PubRelTest extends TestCase
{
    public function testPubRel(): void
    {
        $msg = PubRel::newInstance()
            ->setPacketIdentifier(0x329C);
        $binMsg = pack('CCCC', 0b01100010, 2, 0x32, 0x9C);
        self::assertEquals($binMsg, $msg->encode());
    }
}
