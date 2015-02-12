# The PHP Computer

[![Build Status](https://travis-ci.org/EvanDotPro/PhpComputer.svg?branch=master)](https://travis-ci.org/EvanDotPro/PhpComputer)

This project is the result of procrastination combined with my desire to improve my understandings of the lower level components of computers. Simply put, this is an OOP PHP representation of the lowest level components of a computer. I may or may not add more components as I learn and understand more, but I'll happily accept pull requests.

This project does not aim to have any practical use what-so-ever, aside from possibly entertainment or education value. However, it not necessarily aimed to help _teach_ the concepts it represents, and is likely to make more sense to those who already have a basic understanding of how computers work at a low level. To get a better understanding of these concepts, I highly recommend the book [But How Do It Know](http://www.amazon.com/gp/product/0615303765) by J. Clark Scott.

**Note:** While this could conceivably be evolved into a hardware emulation layer for PHP (maybe?), that is _not_ the goal of this project. Not only have I never looked at a single line of harware virtualization code in my life at the time of writing this, but it would also just be plain idiotic to attempt something like that with PHP. Stop thinking about such horrible things.

Oh, and if you like this, you'll probably also enjoy [JointJS's Logic Circuits](http://www.jointjs.com/demos/logic).

## Install

Via Composer

```json
{
    "require": {
        "evandotpro/php-computer": "dev-master"
    }
}
```


## Usage

#### Bit

In computers, everything is made up of [bits](http://en.wikipedia.org/wiki/Bit). A bit is merely a unit of information (a circuit) that is either on (1) or off (0). That's it. So, we have a bit:

```php
<?php
use Edp\PhpComputer\Bit;

$bit = new Bit;
$bit->on();    // turns the bit on (default state is off)
$bit->off();   // turns the bit off
$bit->state(); // returns the current state of the bit (1 for on, 0 for off)
```

You can think of a bit as a segment of wire that either does or does not carry a charge.

Now, because we are going to hook things up to these bits which are going to need to be notified when the bits change state, we utilize the [Observer pattern](http://en.wikipedia.org/wiki/Observer_pattern). A bit is a "subject" being observed, thus, observers can "attach" to a bit to be notified when it changes state:

```php
<?php
use Edp\PhpComputer\Bit;

$bit = new Bit;

$observer = new SomeObserver; // must implement Edp\PhpComputer\Util\ObserverInterface, which simply has an update() method.
$bit->attach($observer);

$bit->on(); // if the bit was off (it is by default), it is turned on, and $observer->update() is called

$bit->detach($observer); // You can also detach an observer if it is no longer "connected" to that bit.
```

#### NAND Gate

[NAND gates](http://en.wikipedia.org/wiki/NAND_gate) are one of many [logic gates](http://en.wikipedia.org/wiki/Logic_gate) which make up modern electronics and CPUs. NAND gates are significant because technically, you can implement _every_ possible computing operation using one or more NAND gates (they have [functional completeness](http://en.wikipedia.org/wiki/Functional_completeness)).

To put it simply, a NAND gate is a very basic electrical circuit which has two inputs and one output. If both inputs are on, the output is powered off. Any other combination (both inputs off or one on and the other off) will turn the output on. (That's really all you need to know, but if you're wondering how that's electronically possible, NAND gates also have their own constant power source in addition to the inputs. The power from the two inputs acts on transistors to connect the NAND gate's output either to a ground or the power source.)

A NAND gate can be represented with the following truth table:

| Input 1 | Input 2 | Output |
|---------|---------|--------|
|    0    |    0    |    1   |
|    0    |    1    |    1   |
|    1    |    0    |    1   |
|    1    |    1    |    0   |

```php
<?php
use Edp\PhpComputer\Bit;
use Edp\PhpComputer\LogicGate\Nand;

$input1 = new Bit; // off by default
$input2 = new Bit; // off by default

$nand = new Nand;
$nand->attachInputs($input1, $input2);

var_dump($nand->state()) // int(1) (on) because both inputs are off.

$input1->on();
$input2->on();

var_dump($nand->state()) // int(0) (off) because both inputs are on.
```

But wait, there's more! Because a NAND gate outputs a bit, the `Nand` class actually _is_ a `Bit` as well (it extends the `Bit` class). This means we can wire multiple NAND gates together!

```php
<?php
use Edp\PhpComputer\Bit;
use Edp\PhpComputer\LogicGate\Nand;

$input1 = new Bit; // off by default
$input2 = new Bit; // off by default

$nand1 = new Nand;
$nand1->attachInputs($input1, $input2);

$nand2 = new Nand;
$nand2->attachInputs($input1, $nand1); // Now this NAND gate will get input from the $input1 Bit and the output bit of $nand1. Neat, huh?
```

So now you can start doing some really cool shit. For example, creating a single bit of persistent RAM using only four NAND gates!

At the most fundamental level, RAM works like this: You have an input wire and a "set" wire, plus an output wire that always represents the bit being stored. To store a bit, you either turn the input wire on (1) or off (0), then turn the set wire "on" for a brief moment, which tells the circuit to "lock in" whatever state the input wire has.

Here's how that looks with NAND gates:

```php
<?php
use Edp\PhpComputer\Bit;
use Edp\PhpComputer\LogicGate\Nand;

// Create the input and set "wires" (bits)
$setBit   = new Bit;
$inputBit = new Bit;

// Wire up our NAND gates to make some RAM!
$nand1 = Nand;
$nand1->attachInputs($inputBit, $setBit);

$nand2 = new Nand;
$nand2->attachInputs($nand1, $setBit);

$nand3 = new Nand;
$nand4 = new Nand;

$nand3->attachInputs($nand1, $nand4);
$nand4->attachInputs($nand2, $nand3);

// Now we can use it...

$inputBit->on(); // We are going to store a '1'

$setBit->on()->off(); // Momentarily flip the "set" bit on to store the input bit in our RAM circuit.

var_dump($nand3->state()); // int(1)

$inputBit->off(); // Now we are going to update it to '0'... the RAM is not updated to match the input until we flip the set bit on again...
$setBit->on()->off(); // Momentarily flip the "set" bit on to store the input bit in our RAM circuit.

var_dump($nand3->state()); // int(0)

$inputBit->on();

var_dump($nand3->state()); // Still int(0) because the "set" bit is off, thus the input bit is having no effect on our RAM circuit

// As long as the "set" bit is on, $nand3's state will always mirror the input bit.
$setBit->on();
$inputBit->on();
var_dump($nand3->state()); // int(1)
$inputBit->off();
var_dump($nand3->state()); // int(0)
```

#### Memory Bit

As I just demonstrated, you can create a single bit of persistent RAM by combining 4 NAND gates. That's a lot of typing, so there's a `MemoryBit` class which will construct this circuit for you.

```php
<?php
use Edp\PhpComputer\MemoryBit;

$memory = new MemoryBit;
var_dump($memory->read()); // int(1)

$memory->write(0);
var_dump($memory->read()); // int(0)
```

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/EvanDotPro/php-computer/blob/master/CONTRIBUTING.md) for details.

## Credits

- [Evan Coury](https://github.com/EvanDotPro)


## License

The MIT License (MIT). Please see [License File](https://github.com/EvanDotPro/php-computer/blob/master/LICENSE) for more information.
