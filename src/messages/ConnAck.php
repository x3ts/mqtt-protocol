<?php

namespace x3ts\mqtt\protocol\messages;

use x3ts\mqtt\protocol\constants\Types;

/**
 * Class ConnAck
 *
 * @package x3ts\mqtt\protocol\messages
 * @property-read int $connAckFlags
 */
class ConnAck extends MessageBase
{
    public const Accepted = 0;
    public const UnacceptableProtocolLevel = 1;
    public const IdentifierRejected = 2;
    public const ServerUnavailable = 3;
    public const BadUserNameOrPassword = 4;
    public const NotAuthorized = 5;

    public bool $sessionPresent = false;

    public int $ackCode = self::Accepted;

    public function getType(): int
    {
        return Types::ConnAck;
    }

    public function setAckCode(int $ackCode): static
    {
        $this->ackCode = $ackCode;
        return $this;
    }

    public function setSessionPresent(bool $sessionPresent): static
    {
        $this->sessionPresent = $sessionPresent;
        return $this;
    }

    public function getConnAckFlags(): int
    {
        return $this->sessionPresent ? 1 : 0;
    }

    protected function encodeMessageBody(): string
    {
        return pack('CC', $this->getConnAckFlags(), $this->ackCode);
    }

    protected function decodeMessageBody(string $buffer, int $flags): static
    {
        $this->setSessionPresent(self::decodeByte($buffer) > 0);
        $this->setAckCode(self::decodeByte($buffer));
        return $this;
    }
}
