<?php

use Lonesoft\PhpAop\Aop;
use Lonesoft\PhpAop\JointPoint;

class ReplaceTest extends \Tests\Utility\Unit\Baseline
{


    // PUBLIC METHODS

    public function testReplacePublicMethod()
    {
        Aop::replaceMethod($this->className, 'callPublic', 'return \'replaced\';');
        $actual = $this->class->testCallPublic();

        $this->assertEquals('replaced', $actual);
    }


    // PROTECTED METHODS

    public function testReplaceProtectedFunction()
    {
        Aop::replaceMethod($this->className, 'callProtected', 'return \'replaced\';');
        $actual = $this->class->testCallProtected();

        $this->assertEquals('replaced', $actual);
    }


    // PRIVATE METHODS

    public function testReplacePrivateMethod()
    {
        Aop::replaceMethod($this->className, 'callPrivate', 'return \'replaced\';');
        $actual = $this->class->testCallPrivate();

        $this->assertEquals('replaced', $actual);
    }


    // STATIC METHODS

    public function testReplaceStaticMethod()
    {
        Aop::replaceMethod($this->className, 'callStatic', 'return \'replaced\';');
        $actual = $this->class->testCallStatic();

        $this->assertEquals('replaced', $actual);
    }


    // METHODS WITH PARAMETERS

    public function testReplaceMethodWithParameters()
    {
        Aop::replaceMethod($this->className, 'callWithParameters', 'return \'replaced\';');
        $actual = $this->class->testCallWithParameters(1, 'a', false, 'not-null');

        $this->assertEquals('replaced', $actual);
    }


    // METHODS WITH DEFAULT PARAMETERS

    public function testReplaceMethodWithDefaultParameters()
    {
        Aop::replaceMethod($this->className, 'callWithParameters', 'return \'replaced\';');
        $actual = $this->class->testCallWithDefaultParameters();

        $this->assertEquals('replaced', $actual);
    }

    // METHODS WITH TYPED PARAMETERS

    public function testReplaceMethodWithTypedParameters()
    {
        Aop::replaceMethod($this->className, 'callWithTypedParameters', 'return \'replaced\';');
        $actual = $this->class->testCallWithTypedParameters(new stdClass());

        $this->assertEquals('replaced', $actual);
    }

    public function testReplaceMethodWithWrongTypedParametersShouldFail()
    {
        Aop::replaceMethod($this->className, 'callWithTypedParameters', 'return \'replaced\';');

        $message = [
            'Argument 1 passed to ',
            $this->className,
            '::testCallWithTypedParameters() ',
            'must be an instance of stdClass, null given, called in ',
            __FILE__,
            ' on line ',
            __LINE__ + 4
        ];
        $exception = new \TypeError(implode('', $message));
        $this->tester->expectThrowable($exception, function () {
            $this->class->testCallWithTypedParameters(null);
        });
    }


    // EXCEPTIONS

    public function testRewireNonExistingClassShouldThrow()
    {
        $exception = new \Exception('Class \'this_class_does_not_exist\' not found');
        $this->tester->expectThrowable($exception, function () {
            Aop::replaceMethod('this_class_does_not_exist', 'whatever', 'return \'replaced\';');
        });
    }

    public function testRewireNonExistingMethodShouldThrow()
    {
        $exception = new \Exception('Method \'this_method_does_not_exist\' of \'' . $this->className . '\' not found');
        $this->tester->expectThrowable($exception, function () {
            Aop::replaceMethod($this->className, 'this_method_does_not_exist', 'return \'replaced\';');
        });
    }


    // RETURN ARGUMENTS

    public function testReturnArgument()
    {
        Aop::replaceMethod($this->className, 'callPublic', 'return $jointPoint->getArgument(0);');
        $actual = $this->class->callPublic('argument');

        $this->assertEquals('argument', $actual);
    }

    public function testReturnArgumentDefault()
    {
        Aop::replaceMethod($this->className, 'callPublic', 'return $jointPoint->getArgument(1, \'default\');');
        $actual = $this->class->callPublic('argument');

        $this->assertEquals('default', $actual);
    }


    // CALLBACKS

    public function testReplaceCallback()
    {
        $function = [$this->className, 'callStatic'];
        Aop::replaceMethod($this->className, 'callPublic', $function);
        $actual = $this->class->callPublic();

        $this->assertEquals('callStatic', $actual);
    }

    public function testReplaceClosure()
    {
        Aop::replaceMethod($this->className, 'callPublic', function () {
            return 'replaced';
        });
        $actual = $this->class->callPublic();

        $this->assertEquals('replaced', $actual);
    }

    public function testReplaceClosureWithUse()
    {
        $return = 'new return';
        Aop::replaceMethod($this->className, 'callPublic', function (JointPoint $jointPoint) use ($return) {
            return $return;
        });
        $actual = $this->class->callPublic();

        $this->assertEquals($return, $actual);
    }

    public function testReplaceClosureWithThis()
    {
        Aop::replaceMethod($this->className, 'callPublic', function (JointPoint $jointPoint) {
            /** @var mocked $this */
            return $this->callPrivate();
        });
        $actual = $this->class->callPublic();

        $this->assertEquals('callPrivate', $actual);
    }

    public function testReplaceClosureWithStatic()
    {
        Aop::replaceMethod($this->className, 'callPublic', function (JointPoint $jointPoint) {
            /** @var mocked self */
            return self::callStatic();
        });
        $actual = $this->class->callPublic();

        $this->assertEquals('callStatic', $actual);
    }

}
