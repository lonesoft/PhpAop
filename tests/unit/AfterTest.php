<?php

use Lonesoft\PhpAop\Aop;
use Lonesoft\PhpAop\JointPoint;

class AfterTest extends \Tests\Utility\Unit\Baseline
{


    // PUBLIC METHODS

    public function testAfterMethod()
    {
        Aop::afterMethod($this->className, 'callPublic', 'return \'After: \' . $jointPoint->getReturnedValue();');
        $actual = $this->class->testCallPublic();

        $this->assertEquals('After: callPublic', $actual);
    }


    // STATIC METHODS

    public function testBeforeStaticMethod()
    {
        Aop::afterMethod($this->className, 'callStatic', 'return \'After: \' . $jointPoint->getReturnedValue();');
        $actual = $this->class->testCallStatic();

        $this->assertEquals('After: callStatic', $actual);
    }

//    // METHODS WITH PARAMETERS
//
//    public function testBeforeMethodWithoutChangingParameters()
//    {
//        Aop::beforeMethod($this->className, 'callWithParameters', '');
//        $actual = $this->class->testCallWithParameters(1, 'a', false, 'not-null');
//
//        $this->assertEquals('callWithParameters(1, a, , not-null)', $actual);
//    }
//
//    public function testBeforeMethodChangingParameters()
//    {
//        Aop::beforeMethod($this->className, 'callWithParameters', '$jointPoint->setArgument(0, 2);');
//        $actual = $this->class->testCallWithParameters(1, 'a', false, 'not-null');
//
//        $this->assertEquals('callWithParameters(2, a, , not-null)', $actual);
//    }
//
}
