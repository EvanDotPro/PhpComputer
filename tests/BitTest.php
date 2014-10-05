<?php
namespace Edp\PhpComputer;

class BitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Edp\PhpComputer\Bit::state
     */
    public function testDefaultBitStateIsOff()
    {
        $bit = new Bit;
        $this->assertEquals($bit->state(), $bit::OFF);
    }

    /**
     * @covers Edp\PhpComputer\Bit::on
     * @covers Edp\PhpComputer\Bit::off
     */
    public function testTogglingBitTurnsItOnAndOff()
    {
        $bit = new Bit;
        $bit->on();
        $this->assertEquals($bit->state(), $bit::ON);
        $bit->off();
        $this->assertEquals($bit->state(), $bit::OFF);
    }
}
