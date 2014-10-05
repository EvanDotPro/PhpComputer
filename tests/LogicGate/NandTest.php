<?php
namespace Edp\PhpComputer\LogicGate;

use Edp\PhpComputer\Bit;

/**
 * @covers Edp\PhpComputer\LogicGate\Nand
 */
class NandTest extends \PHPUnit_Framework_TestCase
{
    protected $input1;
    protected $input2;

    public function setUp()
    {
        $this->input1 = new Bit;
        $this->input2 = new Bit;
    }

    public function testBothInputsOnTurnsOutputOff()
    {
        $this->input1->on();
        $this->input2->on();

        $nand = new Nand;
        $nand->attachInputs($this->input1, $this->input2);

        $this->assertEquals($nand->state(), $nand::OFF);
    }

    public function testBothInputsOffTurnsOutputOn()
    {
        $this->input1->off();
        $this->input2->off();

        $nand = new Nand;
        $nand->attachInputs($this->input1, $this->input2);

        $this->assertEquals($nand->state(), $nand::ON);
    }

    public function testOneInputOnTurnsOutputOn()
    {
        $this->input1->on();
        $this->input2->off();

        $nand = new Nand;
        $nand->attachInputs($this->input1, $this->input2);

        $this->assertEquals($nand->state(), $nand::ON);
    }

    public function testOutputReflectsChangesToInput()
    {
        $this->input1->off();
        $this->input2->on();

        $nand = new Nand;
        $nand->attachInputs($this->input1, $this->input2);

        $this->assertEquals($nand->state(), $nand::ON);

        $this->input1->on();
        $this->input2->on();

        $this->assertEquals($nand->state(), $nand::OFF);
    }
}
