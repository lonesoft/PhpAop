<?php

use Lonesoft\PhpAop\Aop;
use Lonesoft\PhpAop\JointPoint;

class BeforeTest extends AbstractTest
{


    // PUBLIC METHODS

    public function testBeforeMethod()
    {
        Aop::beforeMethod($this->className, 'callPublic', 'return \'replaced\';');
        $actual = $this->class->testCallPublic();

        $this->assertEquals('callPublic', $actual);
    }


    // STATIC METHODS

    public function testBeforeStaticMethod()
    {
        Aop::beforeMethod($this->className, 'callStatic', 'return \'replaced\';');
        $actual = $this->class->testCallStatic();

        $this->assertEquals('callStatic', $actual);
    }

    // METHODS WITH PARAMETERS

    public function testBeforeMethodWithoutChangingParameters()
    {
        Aop::beforeMethod($this->className, 'callWithParameters', '');
        $actual = $this->class->testCallWithParameters(1, 'a', false, 'not-null');

        $this->assertEquals('callWithParameters(1, a, , not-null)', $actual);
    }

    public function testBeforeMethodChangingParameters()
    {
        Aop::beforeMethod($this->className, 'callWithParameters', '$jointPoint->setArgument(0, 2);');
        $actual = $this->class->testCallWithParameters(1, 'a', false, 'not-null');

        $this->assertEquals('callWithParameters(2, a, , not-null)', $actual);
    }

}
