<?php

namespace x3ts\mqtt\protocol\messages;

use x3ts\mqtt\protocol\traits\OnlyPacketId;
use x3ts\mqtt\protocol\constants\Types;

class PubRel extends MessageBase
{
    use OnlyPacketId;

    public function getType(): int
    {
        return Types::PubRel;
    }

    public function getFlags(): int
    {
        return 0b0010;
    }
}
