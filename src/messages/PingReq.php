<?php

namespace x3ts\mqtt\protocol\messages;

use x3ts\mqtt\protocol\constants\Types;
use x3ts\mqtt\protocol\traits\EmptyRemain;

class PingReq extends MessageBase
{
    use EmptyRemain;

    public function getType(): int
    {
        return Types::PingReq;
    }
}
