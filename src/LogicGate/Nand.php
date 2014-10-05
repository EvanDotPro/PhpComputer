<?php
namespace Edp\PhpComputer\LogicGate;

use Edp\PhpComputer\Util\ObserverInterface;
use Edp\PhpComputer\Bit;

class Nand extends Bit implements ObserverInterface
{
    protected $input1;
    protected $input2;

    public function __construct()
    {
        $this->attach($this); // hack so that $nand->on() and $nand->off() cannot get the NAND into an invalid state
    }

    public function attachInputs(Bit $input1, Bit $input2)
    {
        $this->setInput('input1', $input1);
        $this->setInput('input2', $input2);
        $this->update();
    }

    private function setInput($inputVar, Bit $input)
    {
        if ($this->{$inputVar} instanceof Bit) {
            $this->{$inputVar}->detach($this);
        }

        $this->{$inputVar} = $input->attach($this);
    }

    public function update()
    {
        if ($this->input1->state() === self::ON && $this->input2->state() === self::ON) {
            $this->off();
        } elseif ($this->state() !== self::ON) {
            $this->on();
        }
    }
}
