<?php

namespace x3ts\mqtt\protocol\messages;

use x3ts\mqtt\protocol\constants\Types;
use x3ts\mqtt\protocol\traits\OnlyPacketId;

class UnsubAck extends MessageBase
{
    use OnlyPacketId;

    public function getType(): int
    {
        return Types::UnsubAck;
    }
}
