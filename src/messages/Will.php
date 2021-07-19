<?php

namespace x3ts\mqtt\protocol\messages;

class Will
{
    public function __construct(
        public string $topic,
        public string $message,
        public int $qos = 0,
        public bool $retain = false,
    )
    {
    }
}
