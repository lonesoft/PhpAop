<?php

namespace Lonesoft\PhpAop;

use PhpParser\Node\Expr\Closure;
use ReflectionClass;
use ReflectionMethod;

class Aop
{
    protected static $callables = [];

    /**
     * @param string $className
     * @param string $methodName
     * @param callable|array|string $advice
     */
    public static function replaceMethod($className, $methodName, $advice)
    {
        $instance = new self($className, $methodName, $advice, __FUNCTION__);
        return $instance->rewire();
    }

    /**
     * @param $callbackName
     * @param JointPoint $joinPoint
     * @return mixed
     */
    public static function executeCallback($callbackName, $joinPoint, $context)
    {
        if (isset(self::$callables[$callbackName])) {
            $callable = self::$callables[$callbackName];
            if ($callable instanceof \Closure) {
                $newCallable = $callable->bindTo($context, $context);
                $result = $newCallable->__invoke($joinPoint);
            } else {
                $result = call_user_func($callable, $joinPoint);
            }
            return $result;
        }
    }

    protected static $codeTemplates = [
        'replaceMethod' => [
            '$arguments = func_get_args();',
            '$jointPoint = new ${joinPointClassName}($arguments);',
            '$result = $this->${adviceMethodName}($jointPoint);',
            'return $result;'
        ],
        'executeCallback' => [
            '$result = ${callbackFunctionName}(\'${adviceMethodName}\', $jointPoint, $this);',
            'return $result;'
        ],
    ];

    /**
     * @var string|null
     */
    protected $className;

    /**
     * @var string
     */
    protected $methodName;

    /**
     * @var callable|array|string
     */
    protected $advice;

    /**
     * @var string
     */
    protected $joinType;

    /**
     * @var string
     */
    protected $originalMethodName;

    /**
     * @var string
     */
    protected $adviceMethodName;

    /**
     * @var string
     */
    protected $wrapperCode;

    /**
     * @var callable|array|string
     */
    protected $adviceCode;

    protected $methodFlag;

    /**
     * @var array
     */
    protected $methodParameters;

    /**
     * @param string|null $className
     * @param string $methodName
     * @param callable|array|string $advice
     * @param string $joinType
     */
    protected function __construct($className, $methodName, $advice, $joinType)
    {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->advice = $advice;
        $this->joinType = $joinType;
    }

    /**
     * @return bool
     */
    protected function rewire()
    {
        if (!$this->methodExists()) {
            return false;
        }
        $this->assignAlternativeNames();
        $this->createWrapperCode();
        $this->reflectMethod();
        $this->swapMethods();
        return true;
    }

    /**
     * @return bool
     */
    protected function methodExists()
    {
        if (!class_exists($this->className)) {
            return false;
        }
        if (!method_exists($this->className, $this->methodName)) {
            return false;
        }
        return true;
    }

    protected function assignAlternativeNames()
    {
        $uid = uniqid('', true);
        $suffix = md5($uid);
        $this->originalMethodName = $this->methodName . '_original_' . $suffix;
        $this->adviceMethodName = $this->methodName . '_advice_' . $suffix;
    }

    protected function createWrapperCode()
    {
        if (is_callable($this->advice)) {
            $this->addCallback();
            $this->adviceCode = $this->getTemplate('executeCallback');
        } else {
            $this->adviceCode = $this->advice;
        }
        $this->wrapperCode = $this->getTemplate($this->joinType);
    }

    protected function getTemplate($name)
    {
        $template = (array)self::$codeTemplates[$name];
        $replace = [
            '${originalMethodName}' => $this->originalMethodName,
            '${adviceMethodName}' => $this->adviceMethodName,
            '${joinPointClassName}' => __NAMESPACE__ . '\\JointPoint',
            '${callbackFunctionName}' => __CLASS__ . '::executeCallback',
        ];
        do {
            $template = str_replace(array_keys($replace), array_values($replace), $template, $count);
        } while ($count > 0);
        $template = implode("\n", $template);
        return $template;
    }

    protected function addCallback()
    {
        self::$callables[$this->adviceMethodName] = $this->advice;
    }

    protected function reflectMethod()
    {
        $reflection = new ReflectionClass($this->className);
        $method = $reflection->getMethod($this->methodName);
        $this->methodFlag = $this->getMethodFlag($method);
        $this->methodParameters = $this->getMethodParameters($method);
    }

    protected function swapMethods()
    {
        $adviceCode = implode("\n", (array)$this->adviceCode);
        runkit_method_rename($this->className, $this->methodName, $this->originalMethodName);
        runkit_method_add($this->className, $this->adviceMethodName, '$jointPoint', $adviceCode);
        runkit_method_add($this->className, $this->methodName, $this->methodParameters, $this->wrapperCode, $this->methodFlag);
    }


    protected function getMethodFlag(ReflectionMethod $method)
    {
        if ($method->isPrivate()) {
            $flag = RUNKIT_ACC_PRIVATE;
        } elseif ($method->isProtected()) {
            $flag = RUNKIT_ACC_PROTECTED;
        } else {
            $flag = RUNKIT_ACC_PUBLIC;
        }
        if ($method->isStatic()) {
            $flag = $flag || RUNKIT_ACC_STATIC;;
        }
        return $flag;
    }

    protected function getMethodParameters(ReflectionMethod $method)
    {
        $args = [];
        foreach ($method->getParameters() as $parameter) {
            $class = $parameter->getClass();
            if (isset($class)) {
                $arg = $class->getName() . ' ';
            } else {
                $arg = '';
            }
            $arg .= '$' . $parameter->getName();
            if ($parameter->isDefaultValueAvailable()) {
                $default = $parameter->getDefaultValue();
                $arg .= ' = ';
                switch (gettype($default)) {
                    case 'boolean':
                        $arg .= $default ? 'true' : 'false';
                        break;
                    case 'string':
                        $arg .= '\'' . str_replace('\'', '\\\'', $default) . '\'';
                        break;
                    case 'null':
                        $arg .= 'null';
                        break;
                    default:
                        $arg .= $default;
                }
            }
            $args[] = $arg;
        }
        return implode(', ', $args);
    }

}