<?php

namespace x3ts\mqtt\protocol\traits;

trait OnlyPacketId
{
    use PacketIdentifier;

    abstract protected static function encodeUint16(int $integer): string;

    protected function encodeMessageBody(): string
    {
        return static::encodeUint16($this->packetIdentifier);
    }
}
