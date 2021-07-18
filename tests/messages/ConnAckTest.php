<?php

namespace x3ts\mqtt\tests\messages;

use PHPUnit\Framework\TestCase;
use x3ts\mqtt\protocol\messages\ConnAck;

class ConnAckTest extends TestCase
{
    public function testAccept(): void
    {
        $msg = ConnAck::newInstance()
            ->setAckCode(ConnAck::Accepted)
            ->setSessionPresent(true);
        $binMsg = pack('CCCC',
            0b00100000, // fixed header
            0b00000010, // remain length
            0b00000001, //Connect Acknowledge Flags
            0x00, // return code
        );
        self::assertEquals($binMsg, $msg->encode());
    }
}
