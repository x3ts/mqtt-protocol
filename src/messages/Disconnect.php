<?php

namespace x3ts\mqtt\protocol\messages;

use x3ts\mqtt\protocol\constants\Types;

class Disconnect extends MessageBase
{

    public function getType(): int
    {
        return Types::Disconnect;
    }

    protected function encodeMessageBody(): string
    {
        return '';
    }
}
