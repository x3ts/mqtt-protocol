<?php

namespace x3ts\mqtt\protocol\traits;

trait GetterSetter
{
    public function __get(string $name)
    {
        $getter = 'get' . ucfirst($name);
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    public function __set(string $name, $value)
    {
        $setter = 'set' . ucfirst($name);
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        }
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __isset($name): bool
    {
        return $this->__get($name) !== null;
    }
}
