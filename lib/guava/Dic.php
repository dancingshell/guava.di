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

            //set empty array to push into later
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
    private function setDependencies($className, $dependency)
    {
        //check if param is a object, if true, push new instance of that object into array
        if (is_object($dependency->getClass())) {
            $class = $dependency->getClass()->name;
            array_push($this->dependencies[$className], new $class);
        }
    }

    /**
     * @param String $className
     * @return Array
     */
    private function getDependencies($className)
    {
        if ($this->dependencies[$className] !== array()) {
            return $this->dependencies[$className];
        }
    }

    public function isLoaded($className, $dep = false)
    {
        if (isset($this->loaders[$className])) {
            if ($dep == true) {
                if ($this->dependencies[$className] == array()) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    public function clearCache($className = null)
    {
        if ($className !== null) {
            unset($this->loaders[$className]);
            unset($this->dependencies[$className]);
            if (!isset($this->dependencies[$className]) && !isset($this->loaders[$className])) {
                return true;
            } else {
                return false;
            }

        } else {
            unset($this->loaders);
            unset($this->dependencies);
            if (!isset($this->dependencies) && !isset($this->loaders)) {
                return true;
            } else {
                return false;
            }
        }
    }
}
