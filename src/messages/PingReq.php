<?php

namespace x3ts\mqtt\protocol\messages;

use x3ts\mqtt\protocol\constants\Types;

class PingReq extends MessageBase
{
    public function getType(): int
    {
        return Types::PingReq;
    }

    protected function encodeMessageBody(): string
    {
        return '';
    }
}
