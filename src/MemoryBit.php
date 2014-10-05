<?php
namespace Edp\PhpComputer;

class MemoryBit
{
    protected $set;
    protected $input;
    protected $nand1;
    protected $nand2;
    protected $nand3;
    protected $nand4;

    public function __construct()
    {
        $this->set   = new Bit;
        $this->input = new Bit;

        $this->nand1 = new LogicGate\Nand;
        $this->nand1->attachInputs($this->input, $this->set);

        $this->nand2 = new LogicGate\Nand;
        $this->nand2->attachInputs($this->nand1, $this->set);

        $this->nand3 = new LogicGate\Nand;
        $this->nand4 = new LogicGate\Nand;

        $this->nand3->attachInputs($this->nand1, $this->nand4);
        $this->nand4->attachInputs($this->nand2, $this->nand3);
    }

    public function read()
    {
        return $this->nand3->state();
    }

    public function write($bit)
    {
        $bit ? $this->input->on() : $this->input->off();
        $this->set->on()->off(); // Flip the set bit on real quick to store the input in the circuit
    }
}
