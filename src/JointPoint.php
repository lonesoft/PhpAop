<?php
namespace Lonesoft\PhpAop;

use phpDocumentor\Reflection\Types\AbstractList;

class JointPoint
{
    protected $arguments;
    protected $returnedValue;

    public function __construct($arguments)
    {
        $this->arguments = $arguments;
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    public function getArgument($index, $defaultValue = null)
    {
        if($index < count($this->arguments)){
            return $this->arguments[$index];
        }else{
            return $defaultValue;
        }
    }

    public function setArgument($index, $value)
    {
        $this->arguments[$index] = $value;
    }

    public function getReturnedValue(){
        return $this->returnedValue;
    }
    public function setReturnedValue($value){
        $this->returnedValue = $value;
    }

//    public function &getArguments() {}
//    public function getPropertyName() {}
//    public function getPropertyValue() {}
//    public function setArguments(array $arguments) {}
//    public function getKindOfAdvice() {}
//    public function &getReturnedValue() {}
//    public function &getAssignedValue() {}
//    public function setReturnedValue($value) {}
//    public function setAssignedValue($value) {}
//    public function getPointcut() {}
//    public function getObject() {}
//    public function getClassName() {}
//    public function getMethodName() {}
//    public function getFunctionName() {}
//    public function getException() {}
//    public function process() {}

}
