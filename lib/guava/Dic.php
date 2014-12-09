<?php
namespace guava;
use ReflectionClass;

class Dic
{

    protected $loaders = array();
    protected $dependencies = array();

    /**
     * @param String $className
     */
    public function load($className)
    {

        if (!isset($this->loaders[$className])) {
            $this->loaders[$className] = '\\guava\\Controllers\\'.$className;
        }

        $reflection = new \ReflectionMethod('\\guava\\Controllers\\'.$className, '__construct');
        $params = $reflection->getParameters();
        $this->dependencies[$className] = array();
        foreach ($params as $dependency) {
            $dependency = explode(" ", $dependency);
            var_dump($dependency);
            $this->setDependencies($className, $dependency);
        }
    }

    public function setDependencies($className, $dependency)
    {
        if (is_object($dependency)) {
            if (strpos($dependency, "\\")) {
                $test = explode("\\", $dependency);
                var_dump($test);
                $load = '\\guava\\Dependencies\\'.end(explode("\\", $dependency));
            } else {
                $load = $dependency;
            }
            $instance = new $load;
            array_push($this->dependencies[$className], $instance);

        }
        return $this->dependencies[$className];
    }

    public function getDependencies($className)
    {
        return $this->dependencies[$className];
    }
}
