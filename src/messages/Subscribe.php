<?php

namespace x3ts\mqtt\protocol\messages;

use x3ts\mqtt\protocol\constants\QoS;
use x3ts\mqtt\protocol\traits\PacketIdentifier;
use x3ts\mqtt\protocol\constants\Types;

/**
 * Class Subscribe
 *
 * @package x3ts\mqtt\protocol\messages
 */
class Subscribe extends MessageBase
{
    use PacketIdentifier;

    public function getType(): int
    {
        return Types::Subscribe;
    }

    public function getFlags(): int
    {
        return 0b0010;
    }

    public array $topicQosPairs = [];

    public function addTopicFilter(string $topicFilter, int $qos = QoS::AT_MOST_ONCE): static
    {
        assert($qos >= QoS::AT_MOST_ONCE && $qos <= QoS::EXACTLY_ONCE);
        $this->topicQosPairs[] = [$topicFilter, $qos];
        return $this;
    }

    protected function encodeMessageBody(): string
    {
        $remain = self::encodeUint16($this->packetIdentifier);
        assert(count($this->topicQosPairs) > 0);
        foreach ($this->topicQosPairs as $filter) {
            $remain .= self::encodeUTF8Str($filter[0]) . self::encodeByte($filter[1]);
        }
        return $remain;
    }
}
