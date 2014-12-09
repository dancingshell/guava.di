<?php
namespace guava;
use ReflectionClass;

class Dic
{

    protected $loaders = array();
    protected $dependencies = array();

    /**
     * @param String $className
     * @return Array
     */
    public function load($className)
    {
        if (!isset($this->loaders[$className])) {
            //set the name of the class in the loaders array, next time dependencies will already be loaded
            $this->loaders[$className] = '\\guava\\Controllers\\'.$className;

            //create reflection class to inspect the __constructor parameters of the given class tp find dependencies
            $reflection = new \ReflectionClass('\\guava\\Controllers\\'.$className);
            $constructor = $reflection->getConstructor();
            $params = $constructor->getParameters();

            $this->dependencies[$className] = array();
            foreach ($params as $dependency) {
                $this->setDependencies($className, $dependency);
            }
        }
        return $this->getDependencies($className);
    }

    /**
     * @param String $className
     * @param \ReflectionParameter|Object $dependency
     */
    public function setDependencies($className, $dependency)
    {
        if (is_object($dependency)) {
            $class = $dependency->getClass()->name;
            array_push($this->dependencies[$className], new $class);
        }
    }

    /**
     * @param String $className
     * @return Array
     */
    public function getDependencies($className)
    {
        return $this->dependencies[$className];
    }
}
