<?php

namespace x3ts\mqtt\protocol\traits;

/**
 * Trait PacketIdentifier
 *
 * @package x3ts\mqtt\protocol\traits
 * @property-read int $packetIdentifier
 */
trait PacketIdentifier
{
    protected int $packetIdentifier = 0;

    public function setPacketIdentifier(int $id): static
    {
        assert($id >= 0 && $id < 65536);
        $this->packetIdentifier = $id;
        return $this;
    }

    public function genPacketIdentifier(?callable $hasTaken = null): static
    {
        $id = random_int(0, 65535);
        if (is_callable($hasTaken)) {
            while ($hasTaken($id)) {
                $id = random_int(0, 65535);
            }
        }

        $this->packetIdentifier = $id;
        return $this;
    }
}
