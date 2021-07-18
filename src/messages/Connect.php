<?php

namespace x3ts\mqtt\protocol\messages;

use x3ts\mqtt\protocol\constants\QoS;
use x3ts\mqtt\protocol\constants\Types;

/**
 * Class Connect
 *
 * @package x3ts\mqtt\protocol\messages
 *
 * @property-read int $connectFlags
 */
class Connect extends MessageBase
{
    public bool $cleanSession = false;

    public int $keepAlive = 0;

    public string $clientIdentifier;

    public string $willTopic = '';

    public string $willMessage = '';

    public bool $willRetain = false;

    public int $willQoS = QoS::AT_LEAST_ONCE;

    public string $username = '';

    public string $password = '';

    public function getType(): int
    {
        return Types::Connect;
    }

    public function setClientIdentifier(string $clientIdentifier): static
    {
        $this->clientIdentifier = $clientIdentifier;
        return $this;
    }

    public function setWillTopic(string $willTopic): static
    {
        $this->willTopic = $willTopic;
        return $this;
    }

    public function setWillMessage(string $willMessage): static
    {
        $this->willMessage = $willMessage;
        return $this;
    }

    public function setWillRetain(bool $willRetain): static
    {
        $this->willRetain = $willRetain;
        return $this;
    }

    public function setWillQoS(int $willQoS): static
    {
        $this->willQoS = $willQoS;
        return $this;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function setCleanSession(bool $cleanSession): static
    {
        $this->cleanSession = $cleanSession;
        return $this;
    }

    public function getProtocolName(): string
    {
        return 'MQTT';
    }

    public function getProtocolLevel(): string
    {
        return self::VER_3_1_1;
    }

    public function getConnectFlags(): int
    {
        $flag = 0;
        if ($this->username !== '') {
            $flag |= 0b10000000;
        }
        if ($this->password !== '') {
            $flag |= 0b01000000;
        }
        if ($this->willTopic || $this->willMessage) {
            if ($this->willRetain) {
                $flag |= 0b00100000;
            }
            $flag |= $this->willQoS << 3;
            $flag |= 0b00000100;
        }
        if ($this->cleanSession) {
            $flag |= 0b00000010;
        }
        return $flag;
    }

    protected function encodeMessageBody(): string
    {
        $remain = self::encodeUTF8Str($this->getProtocolName()) .
            pack('C', $this->getProtocolLevel()) .
            pack('C', $this->getConnectFlags()) .
            self::encodeUint16($this->keepAlive);
        //  ^------ header | payload -------v
        $remain .= self::encodeUTF8Str($this->clientIdentifier);
        $hasWill = $this->willTopic || $this->willMessage;
        if ($hasWill) {
            $remain .= self::encodeUTF8Str($this->willTopic);
            $remain .= self::encodeUTF8Str($this->willMessage);
        }
        if ($this->username !== '') {
            $remain .= self::encodeUTF8Str($this->username);
        }
        if ($this->password !== '') {
            $remain .= self::encodeUTF8Str($this->password);
        }
        return $remain;
    }

    public function setKeepAlive(int $keepAlive): static
    {
        $this->keepAlive = $keepAlive;
        return $this;
    }

    public function disableWill(): static
    {
        $this->willTopic = '';
        $this->willMessage = '';
        $this->willRetain = false;
        $this->willQoS = QoS::AT_MOST_ONCE;
        return $this;
    }
}
