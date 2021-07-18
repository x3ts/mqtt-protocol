<?php

namespace x3ts\mqtt\protocol\constants;

abstract class QoS
{
    public const AT_MOST_ONCE = 0;
    public const AT_LEAST_ONCE = 1;
    public const EXACTLY_ONCE = 2;
}
