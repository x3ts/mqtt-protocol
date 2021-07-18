<?php

namespace x3ts\mqtt\protocol\messages;

use x3ts\mqtt\protocol\constants\QoS;
use x3ts\mqtt\protocol\constants\Types;
use x3ts\mqtt\protocol\traits\PacketIdentifier;

class SubAck extends MessageBase
{
    use PacketIdentifier;

    public function getType(): int
    {
        return Types::SubAck;
    }

    public array $returnCodes = [];

    public function addReturnCode(int $maximumQos): static
    {
        $this->returnCodes[] = $maximumQos;
        return $this;
    }

    protected function encodeMessageBody(): string
    {
        $remain = self::encodeUint16($this->packetIdentifier);
        assert(count($this->returnCodes) > 0);
        foreach ($this->returnCodes as $code) {
            assert(in_array($code, [QoS::AT_MOST_ONCE, QoS::AT_LEAST_ONCE, QoS::EXACTLY_ONCE, 0x80], true));
            $remain .= self::encodeByte($code);
        }
        return $remain;
    }
}
