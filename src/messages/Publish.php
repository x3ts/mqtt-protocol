<?php

namespace x3ts\mqtt\protocol\messages;

use x3ts\mqtt\protocol\constants\QoS;
use x3ts\mqtt\protocol\traits\PacketIdentifier;
use x3ts\mqtt\protocol\constants\Types;

class Publish extends MessageBase
{
    use PacketIdentifier;

    public bool $dup = false;

    public int $qos = QoS::AT_MOST_ONCE;

    public bool $retain = false;

    public function getType(): int
    {
        return Types::Publish;
    }

    public function getFlags(): int
    {
        $flag = 0;
        if ($this->qos > QoS::AT_MOST_ONCE && $this->dup) {
            $flag |= 0b00001000;
        }
        $flag |= ($this->qos & 0b00000011) << 1;
        if ($this->retain) {
            $flag |= 0b00000001;
        }
        return $flag;
    }

    public function setDup(bool $dup): static
    {
        $this->dup = $dup;
        return $this;
    }

    public function setQoS(int $qos): static
    {
        assert($qos >= QoS::AT_MOST_ONCE && $qos <= QoS::EXACTLY_ONCE);
        $this->qos = $qos;
        return $this;
    }

    public function setRetain(bool $retain): static
    {
        $this->retain = $retain;
        return $this;
    }

    public string $topic = '';

    public function setTopic(string $topic): static
    {
        $this->topic = $topic;
        return $this;
    }

    public string $payload = '';

    public function setPayload(string $payload): static
    {
        $this->payload = $payload;
        return $this;
    }

    protected function encodeMessageBody(): string
    {
        assert($this->topic !== '');
        $remain = self::encodeUTF8Str($this->topic);
        if ($this->qos > QoS::AT_MOST_ONCE) {
            $remain .= self::encodeUint16($this->packetIdentifier);
        }
        // variable header / payload
        $remain .= $this->payload;
        return $remain;
    }

    protected function decodeMessageBody(string $buffer, int $flags): static
    {
        $this->setDup($flags & 0b00001000 > 0);
        $this->setQoS(($flags & 0b00000110) >> 1);
        $this->setRetain($flags & 0b00000001 > 0);
        $this->setTopic(self::decodeUTF8Str($buffer));
        if ($this->qos > 0) {
            $this->setPacketIdentifier(self::decodeUint16($buffer));
        }
        $this->setPayload($buffer);
        return $this;
    }
}
