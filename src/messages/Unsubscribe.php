<?php

namespace x3ts\mqtt\protocol\messages;

use x3ts\mqtt\protocol\constants\Types;
use x3ts\mqtt\protocol\traits\PacketIdentifier;

class Unsubscribe extends MessageBase
{
    use PacketIdentifier;

    public function getType(): int
    {
        return Types::Unsubscribe;
    }

    public function getFlags(): int
    {
        return 0b0010;
    }

    public $topics = [];

    public function addTopic(string $topic): static
    {
        $this->topics[] = $topic;
        return $this;
    }

    protected function encodeMessageBody(): string
    {
        $remain = self::encodeUint16($this->packetIdentifier);
        assert(count($this->topics) > 0);
        foreach ($this->topics as $topic) {
            $remain .= self::encodeUTF8Str($topic);
        }
        return $remain;
    }

    protected function decodeMessageBody(string $buffer, int $flags): static
    {
        $this->packetIdentifier = self::decodeUint16($buffer);
        while ($buffer !== '') {
            $this->topics[] = self::decodeUTF8Str($buffer);
        }
        return $this;
    }
}
