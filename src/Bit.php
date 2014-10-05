<?php
namespace Edp\PhpComputer;

use Edp\PhpComputer\Util\SubjectTrait;

class Bit
{
    use SubjectTrait;

    const ON  = 1;
    const OFF = 0;

    private $state = self::OFF;

    public function on()
    {
        if ($this->state === self::ON) {
            return $this; // prevent recursion
        }

        $this->state = self::ON;
        $this->notify();

        return $this;
    }

    public function off()
    {
        if ($this->state === self::OFF) {
            return $this; // prevent recursion
        }

        $this->state = self::OFF;
        $this->notify();

        return $this;
    }

    public function state()
    {
        return $this->state;
    }
}
