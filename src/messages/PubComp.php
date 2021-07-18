<?php

namespace x3ts\mqtt\protocol\messages;

use x3ts\mqtt\protocol\traits\OnlyPacketId;
use x3ts\mqtt\protocol\constants\Types;

class PubComp extends MessageBase
{
    use OnlyPacketId;

    public function getType(): int
    {
        return Types::PubComp;
    }
}
