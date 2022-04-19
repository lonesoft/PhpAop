<?php
namespace Tests\Utility\Unit;

use Lonesoft\PhpAop\Aop;
use Lonesoft\PhpAop\JointPoint;

/**
 * @method callPublic()
 * @method callProtected()
 * @method callPrivate()
 * @static @method callStatic()
 * @method callWithParameters($integer = 1, $string = 'default', $boolean = true, $null = null)
 * @method callWithTypedParameters(stdClass $class)
 *
 * @method testCallPublic()
 * @method testCallProtected()
 * @method testCallPrivate()
 * @method testCallStatic()
 * @method testCallWithParameters($integer, $string, $boolean, $null)
 * @method testCallWithDefaultParameters()
 * @method testCallWithTypedParameters(stdClass $class)
 */

abstract class Baseline extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $className;
    /**
     * @var Mocked
     */
    protected $class;

    protected function _before()
    {
        $uid = uniqid('',true);
        $this->className = 'test_' . md5($uid);
        $code = [
            'class ' . $this->className . '{',
            '    public function callPublic(){',
            '        return __FUNCTION__;',
            '    }',
            '    public function callAnotherPublic(){',
            '        return __FUNCTION__;',
            '    }',
            '    protected function callProtected(){',
            '        return __FUNCTION__;',
            '    }',
            '    private function callPrivate(){',
            '        return __FUNCTION__;',
            '    }',
            '    public static function callStatic(){',
            '        return __FUNCTION__;',
            '    }',
            '    public function callWithParameters($integer = 1, $string = \'default\', $boolean = true, $null = null){',
            '        return __FUNCTION__ . \'(\' . $integer . \', \' . $string . \', \' . $boolean . \', \' . $null . \')\';',
            '    }',
            '    public function callWithTypedParameters(stdClass $class){',
            '        return __FUNCTION__;',
            '    }',
            '',
            '    public function testCallPublic(){',
            '        return $this->callPublic();',
            '    }',
            '    public function testCallProtected(){',
            '        return $this->callProtected();',
            '    }',
            '    public function testCallPrivate(){',
            '        return $this->callPrivate();',
            '    }',
            '    public function testCallStatic(){',
            '        return self::callStatic();',
            '    }',
            '    public function testCallWithParameters($integer, $string, $boolean, $null){',
            '        return $this->callWithParameters($integer, $string, $boolean, $null);',
            '    }',
            '    public function testCallWithDefaultParameters(){',
            '        return $this->callWithParameters();',
            '    }',
            '    public function testCallWithTypedParameters(stdClass $class){',
            '        return $this->callWithTypedParameters($class);',
            '    }',
            '}'
        ];
        eval(implode("\n", $code));
        $this->class = new $this->className();
    }

    protected function _after()
    {
    }

}
