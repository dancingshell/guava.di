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
                $depReflection = new \ReflectionClass($dependency->getClass()->name);
                if ($depReflection->isAbstract() && $configs != false) {
                    foreach($configs as $config) {
                        $parent = end(explode($depReflection->name,"\\"));
                        $lookup = $this->registry[$className][$parent][$config];
                        if (is_callable($lookup)) {
                             $dependency = $lookup;
                        };
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
        //check if param is a object, if true, push new instance of that object into array
        if (is_object($dependency->getClass())) {
            $class = $dependency->getClass()->name;
            array_push($this->dependencies[$className], new $class);
        } else (is_callable($dependency, false, $dependencyCall)) {
            $result = call_user_func('dependencyCall');
            array_push($this->dependencies[$className], $result);
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
        var_dump($parentName);
        if (!isset($this->registry[$parentName])) {
            $this->registry[$parentName] = array();
            if (!isset($this->registry[$className][$parentName][$spec])) {
                $this->registry[$className][$parentName][$spec] = array();
            }
        }
        $specClosure = function() use($specLocation) {
            return new $specLocation;
        };
        array_push($this->registry[$className][$parentName][$spec], $specClosure);
    }

    public function lazyGet()
    {

    }
}
