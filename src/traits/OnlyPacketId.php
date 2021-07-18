<?php

namespace x3ts\mqtt\protocol\traits;

trait OnlyPacketId
{
    use PacketIdentifier;

    abstract protected static function encodeUint16(int $integer): string;

    abstract protected static function decodeUint16(string &$buffer): int;

    protected function encodeMessageBody(): string
    {
        return static::encodeUint16($this->packetIdentifier);
    }

    protected function decodeMessageBody(string $buffer, int $flags): static
    {
        $this->packetIdentifier = static::decodeUint16($buffer);
        return $this;
    }
}
