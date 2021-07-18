<?php

namespace x3ts\mqtt\protocol\messages;

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

    public function encode(): string
    {
        return pack('C', $this->getFixedHeader()) .
            self::encodeRemain($this->encodeMessageBody());
    }

    protected static function encodeByte(int $byte): string
    {
        return pack('C', $byte);
    }

    protected static function encodeUTF8Str(string $utfString): string
    {
        $length = strlen($utfString);
        assert($length >= 0 && $length <= 65535);
        return pack('n', strlen($utfString)) . $utfString;
    }

    protected static function encodeUint16(int $integer): string
    {
        assert($integer >= 0 && $integer <= 65535);
        return pack('n', $integer);
    }

    protected static function encodeRemain(string $remain): string
    {
        return self::encodeVariableLength(strlen($remain)) . $remain;
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

    public static function newInstance(): static
    {
        return new static();
    }
}
