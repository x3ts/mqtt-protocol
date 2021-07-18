<?php

namespace x3ts\mqtt\protocol\constants;

abstract class Types
{
    public const Connect = 1;
    public const ConnAck = 2;
    public const Publish = 3;
    public const PubAck = 4;
    public const PubRec = 5;
    public const PubRel = 6;
    public const PubComp = 7;
    public const Subscribe = 8;
    public const SubAck = 9;
    public const Unsubscribe = 10;
    public const UnsubAck = 11;
    public const PingReq = 12;
    public const PingResp = 13;
    public const Disconnect = 14;
}
