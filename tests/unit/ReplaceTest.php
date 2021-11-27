<?php

use Lonesoft\PhpAop\Aop;
use Lonesoft\PhpAop\JointPoint;

class ReplaceTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $className;
    protected $class;

    protected function _before()
    {
        $uid = uniqid('',true);
        $this->className = 'test_' . md5($uid);
        $code = [
            'class ' . $this->className . '{',
            '    public function testMe(){',
            '        return __FUNCTION__;',
            '    }',
            '    public function testCallPrivate(){',
            '        return $this->callPrivate();',
            '    }',
            '    private function callPrivate(){',
            '        return __FUNCTION__;',
            '    }',
            '    public static function callStatic(){',
            '        return __FUNCTION__;',
            '    }',
            '}'
        ];
        eval(implode("\n", $code));
        $this->class = new $this->className();
    }

    protected function _after()
    {
    }

    public function testCallPublicFunction()
    {
        $actual = $this->class->testMe();

        $this->assertEquals('testMe', $actual);
    }

    public function testCallPrivateFunction()
    {
        $actual = $this->class->testCallPrivate();

        $this->assertEquals('callPrivate', $actual);
    }

    public function testCanRewireExistingMethod()
    {
        Aop::replaceMethod($this->className, 'testMe', 'return \'replaced\';');
    }

    public function testCanRewirePrivateMethod()
    {
        Aop::replaceMethod($this->className, 'callPrivate', 'return \'replaced\';');
    }

    public function testRewireNonExistingClassShouldThrow()
    {
        $exception = new \Exception('Class \'this_class_does_not_exist\' not found');
        $this->tester->expectException($exception, function(){
            Aop::replaceMethod('this_class_does_not_exist', 'whatever', 'return \'replaced\';');
        });
    }

    public function testRewireNonExistingMethodShouldThrow()
    {
        $exception = new \Exception('Method \'this_method_does_not_exist\' of \'' . $this->className . '\' not found');
        $this->tester->expectException($exception, function(){
            Aop::replaceMethod($this->className, 'this_method_does_not_exist', 'return \'replaced\';');
        });
    }

    public function testReplacePublicFunction()
    {
        Aop::replaceMethod($this->className, 'testMe', 'return \'replaced\';');
        $actual = $this->class->testMe();

        $this->assertEquals('replaced', $actual);

    }

    public function testReturnArgument()
    {
        Aop::replaceMethod($this->className, 'testMe', 'return $jointPoint->getArgument(0);');
        $actual = $this->class->testMe('argument');

        $this->assertEquals('argument', $actual);

    }

    public function testReturnArgumentDefault()
    {
        Aop::replaceMethod($this->className, 'testMe', 'return $jointPoint->getArgument(1, \'default\');');
        $actual = $this->class->testMe('argument');

        $this->assertEquals('default', $actual);

    }

    public function testReplacePrivateFunction(){
        Aop::replaceMethod($this->className, 'callPrivate', 'return \'replaced\';');
        $actual = $this->class->testCallPrivate();

        $this->assertEquals('replaced', $actual);

    }

    public function testReplaceCallback()
    {
        $function = [$this->className, 'callStatic'];
        Aop::replaceMethod($this->className, 'testMe', $function);
        $actual = $this->class->testMe();

        $this->assertEquals('callStatic', $actual);
    }

    public function testReplaceClosure()
    {
        Aop::replaceMethod($this->className, 'testMe', function(){
            return 'replaced';
        });
        $actual = $this->class->testMe();

        $this->assertEquals('replaced', $actual);
    }

    public function testReplaceClosureWithUse()
    {
        $return = 'new return';
        Aop::replaceMethod($this->className, 'testMe', function(JointPoint $jointPoint) use($return){
            return $return;
        });
        $actual = $this->class->testMe();

        $this->assertEquals($return, $actual);
    }

    public function testReplaceClosureWithThis()
    {
        Aop::replaceMethod($this->className, 'testMe', function(JointPoint $jointPoint){
            return $this->callPrivate();
        }, Aop::NEW_SCOPE_THIS);
        $actual = $this->class->testMe();

        $this->assertEquals('callPrivate', $actual);
    }

}
