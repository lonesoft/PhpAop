<?php

use Lonesoft\PhpAop\Aop;
use Lonesoft\PhpAop\JointPoint;

class BaselineTest extends \Tests\Utility\Unit\Baseline
{


    // PUBLIC METHODS

    public function testCallPublicMethod()
    {
        $actual = $this->class->testCallPublic();

        $this->assertEquals('callPublic', $actual);
    }


    // PROTECTED METHODS

    public function testCallProtectedMethod()
    {
        $actual = $this->class->testCallProtected();

        $this->assertEquals('callProtected', $actual);
    }


    // PRIVATE METHODS

    public function testCallPrivateMethod()
    {
        $actual = $this->class->testCallPrivate();

        $this->assertEquals('callPrivate', $actual);
    }


    // STATIC METHODS

    public function testCallStaticMethod()
    {
        $actual = $this->class->testCallStatic();

        $this->assertEquals('callStatic', $actual);
    }


    // METHODS WITH PARAMETERS

    public function testCallMethodWithParameters()
    {
        $actual = $this->class->testCallWithParameters(1, 'a', false, 'not-null');

        $this->assertEquals('callWithParameters(1, a, , not-null)', $actual);
    }


    // METHODS WITH DEFAULT PARAMETERS

    public function testCallMethodWithDefaultParameters()
    {
        $actual = $this->class->testCallWithDefaultParameters();

        $this->assertEquals('callWithParameters(1, default, 1, )', $actual);
    }


    // METHODS WITH TYPED PARAMETERS

    public function testCallMethodWithTypedParameters()
    {
        $actual = $this->class->testCallWithTypedParameters(new stdClass());

        $this->assertEquals('callWithTypedParameters', $actual);
    }

}
