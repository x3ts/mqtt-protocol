<?php

namespace x3ts\mqtt\protocol\messages;

use Exception;
use MongoDB\BSON\Type;
use x3ts\mqtt\protocol\constants\Types;
use x3ts\mqtt\protocol\exceptions\MalformedRemainLengthException;
use x3ts\mqtt\protocol\traits\GetterSetter;

/**
 * Class MessageBase
 *
 * @package x3ts\mqtt\protocol\messages
 * @property-read int    $type
 * @property-read int    $flags
 * @property-read string $fixedHeader
 */
abstract class MessageBase
{
    use GetterSetter;

    public const VER_3_1_1 = 0x04;

    abstract public function getType(): int;

    public function getFlags(): int
    {
        return 0;
    }

    public function getFixedHeader(): string
    {
        return $this->getType() << 4 | $this->getFlags();
    }

    abstract protected function encodeMessageBody(): string;

    abstract protected function decodeMessageBody(string $buffer, int $flags): static;

    public static function decode(string &$buffer): static
    {
        $fixedHeader = ord($buffer[0]);
        $type = ($fixedHeader & 0b11110000) >> 4;
        $flags = $fixedHeader & 0b00001111;
        $buffer = substr($buffer, 1);
        $remain = self::decodeRemain($buffer);
        return match ($type) {
            Types::Connect => Connect::newInstance()->decodeMessageBody($remain, $flags),
            Types::ConnAck => ConnAck::newInstance()->decodeMessageBody($remain, $flags),
            Types::Publish => Publish::newInstance()->decodeMessageBody($remain, $flags),
            Types::PubAck => PubAck::newInstance()->decodeMessageBody($remain, $flags),
            Types::PubRec => PubRec::newInstance()->decodeMessageBody($remain, $flags),
            Types::PubRel => PubRel::newInstance()->decodeMessageBody($remain, $flags),
            Types::PubComp => PubComp::newInstance()->decodeMessageBody($remain, $flags),
            Types::Subscribe => Subscribe::newInstance()->decodeMessageBody($remain, $flags),
            Types::SubAck => SubAck::newInstance()->decodeMessageBody($remain, $flags),
            Types::Unsubscribe => Unsubscribe::newInstance()->decodeMessageBody($remain, $flags),
            Types::UnsubAck => UnsubAck::newInstance()->decodeMessageBody($remain, $flags),
            Types::PingReq => PingReq::newInstance()->decodeMessageBody($remain, $flags),
            Types::PingResp => PingResp::newInstance()->decodeMessageBody($remain, $flags),
            Types::Disconnect => Disconnect::newInstance()->decodeMessageBody($remain, $flags),
        };
    }

    public function encode(): string
    {
        return pack('C', $this->getFixedHeader()) .
            self::encodeRemain($this->encodeMessageBody());
    }

    protected static function encodeByte(int $byte): string
    {
        return pack('C', $byte);
    }

    protected static function decodeByte(string &$buffer): int
    {
        assert($buffer !== '');
        $byte = ord($buffer[0]);
        $buffer = substr($buffer, 1);
        return $byte;
    }

    protected static function encodeUTF8Str(string $utfString): string
    {
        $length = strlen($utfString);
        assert($length >= 0 && $length <= 65535);
        return pack('n', strlen($utfString)) . $utfString;
    }

    protected static function decodeUTF8Str(string &$buffer): string
    {
        $length = self::decodeUint16($buffer);
        if ($length === 0) {
            return '';
        }
        $str = substr($buffer, 0, $length);
        $buffer = substr($buffer, $length);
        return $str;
    }

    protected static function encodeUint16(int $integer): string
    {
        assert($integer >= 0 && $integer <= 65535);
        return pack('n', $integer);
    }

    protected static function decodeUint16(string &$buffer): int
    {
        assert(strlen($buffer) >= 2);
        $r = unpack('nUint16', $buffer);
        if ($r === false) {
            throw new MalformedRemainLengthException('decode Uint16 error');
        }
        $uint16 = $r['Uint16'];
        $buffer = substr($buffer, 2);
        return $uint16;
    }

    protected static function encodeRemain(string $remain): string
    {
        return self::encodeVariableLength(strlen($remain)) . $remain;
    }

    protected static function decodeRemain(string &$buffer): string
    {
        $length = 0;
        $noLength = self::decodeVariableLength($buffer, $length);
        $remain = substr($noLength, 0, $length);
        $buffer = substr($noLength, $length);
        return $remain;
    }

    protected static function encodeVariableLength(int $length): string
    {
        assert($length >= 0 && $length <= 268435455);
        $x = $length;
        $buffer = '';
        do {
            $byte = $x % 128;
            $x = (int) ($x / 128);
            if ($x > 0) {
                $byte |= 128;
            }
            $buffer .= chr($byte);
        } while ($x > 0);
        return $buffer;
    }

    protected static function decodeVariableLength(string $buffer, int &$length): string
    {
        $multiplier = 1;
        $value = 0;
        $p = 0;
        do {
            $byte = ord($buffer[$p++]);
            $value += ($byte & 127) * $multiplier;
            $multiplier *= 128;
            if ($multiplier > 128 * 128 * 128) {
                throw new MalformedRemainLengthException('malformed message remain length');
            }
        } while (($byte & 128) > 0);
        $length = (int) $value;
        return substr($buffer, $p);
    }

    public static function newInstance(): static
    {
        return new static();
    }
}
