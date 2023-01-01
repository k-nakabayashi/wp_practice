<?php

final class MemberLevel extends Enum
{
    const ADMIN = 1;
    const HEALER = 1;
    const STANDARD = 2;
}


# 以下、utils
# https://qiita.com/Hiraku/items/71e385b56dcaa37629fe
abstract class Enum
{
    private $scalar;

    public function __construct($value)
    {
        $ref = new ReflectionObject($this);
        $consts = $ref->getConstants();
        if (! in_array($value, $consts, true)) {
            throw new InvalidArgumentException;
        }

        $this->scalar = $value;
    }

    final public static function __callStatic($label, $args)
    {
        $class = get_called_class();
        $const = constant("$class::$label");
        return new $class($const);
    }

    public function __invoke()
    {
        return $this->scalar;
    }

    final public function __toString()
    {
        return (string)$this->scalar;
    }
}