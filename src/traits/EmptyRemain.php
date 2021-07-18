<?php

namespace x3ts\mqtt\protocol\traits;

trait EmptyRemain
{
    protected function encodeMessageBody(): string
    {
        return '';
    }

    protected function decodeMessageBody(string $buffer, int $flags): static
    {
        return $this;
    }
}
