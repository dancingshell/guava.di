<?php
namespace guava;
use ReflectionClass;

class Dic
{
    protected $loaders = array();
    protected $registry = array();
    protected $dependencies = array();

    /**
     * @param String $className
     * @return Array
     */
    public function load($className)
    {
        $configs = func_get_args();
        if (count($configs) > 1) {
           array_shift($configs);
        } else {
            $configs = false;
        }

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
                // remove all parameters that aren't objects and find classes that are abstract
                if ($dependency->getClass()) {
                    $dependency = ucfirst($dependency->getName());
                    $depReflection = new \ReflectionClass('\\guava\\Dependencies\\'.$dependency);

                    // check registry for classes declared under abstract classes
                    if (($depReflection->isAbstract()) && ($configs != false)) {
                        foreach ($configs as $config) {
                            $parent = $depReflection->name;
                            $lookup = $this->registry[$className][$parent][$config];
                            if (is_callable($lookup)) {
                                $dependency = $lookup;
                            };
                        }
                    }
                }
                $this->setDependencies($className, $dependency);
            }
        }
        return $this->getDependencies($className);
    }

    /**
     * @param String $className
     * @param \ReflectionParameter|Object|Callable $dependency
     */
    private function setDependencies($className, $dependency)
    {
//        check if param is a object, if true, push new instance of that object into array
        if (is_callable($dependency)) {
            $this->lazyGet($className, $dependency);
        } elseif (is_string($dependency)) {
            $class = '\\guava\\Dependencies\\'.$dependency;
            array_push($this->dependencies[$className], new $class);
        }
    }

    /**
     * @param String $className
     * @return Array
     */
    public function getDependencies($className)
    {
        if ($this->dependencies[$className] !== array()) {
            return $this->dependencies[$className];
        }
    }

    /**
     * @param $className
     * @param bool $dep
     * @return bool
     */
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

    /**
     * @param null|string $className
     * @return bool
     */
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

    public function lazySet($className, $spec, $specLocation)
    {
        $reflection = new \ReflectionClass($specLocation);
        $parent = $reflection->getParentClass();
        $parentName = $parent->name;

        $specClosure = function() use($specLocation) {
            return new $specLocation;
        };
        $this->registry[$className][$parentName][$spec] = $specClosure;
    }

    public function lazyGet($className, $dependency)
    {
        $result = call_user_func($dependency);
        array_push($this->dependencies[$className], $result);
    }
}
