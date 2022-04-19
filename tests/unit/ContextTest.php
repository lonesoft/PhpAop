<?php

use Lonesoft\PhpAop\Aop;
use Lonesoft\PhpAop\JointPoint;

class ContextTest extends \Tests\Utility\Unit\Baseline
{


    public function testCallPublicMethod()
    {
        Aop::replaceMethod($this->className, 'callPublic', 'return $this->callAnotherPublic();');
        $actual = $this->class->testCallPublic();

        $this->assertEquals('callAnotherPublic', $actual);
    }
}
