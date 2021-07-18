<?php

namespace x3ts\mqtt\protocol\messages;

use x3ts\mqtt\protocol\constants\Types;

class PingResp extends MessageBase
{

    public function getType(): int
    {
        return Types::PingResp;
    }

    protected function encodeMessageBody(): string
    {
        return '';
    }
}
