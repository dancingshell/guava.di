<?php
namespace guava;

class DIContainer {

    protected $loaders = array();
    protected $dependencies = array();

    /**
     * @param String $className
     */
    public function load($className)
    {

        if (!isset($this->loaders[$className])) {
            $this->loaders[$className] = new $className;
        }

        $reflection = new ReflectionClass($this->loaders[$className]);
        $params = $reflection->getConstructor();

        $this->dependencies[$className] = array();
        foreach ($params as $dependency) {
            $this->setDependencies($className, $dependency);
        }
    }

    public function setDependencies($className, $dependency)
    {
        if (is_object($dependency)) {
            $load = explode("\\", "$dependency");
            $instance = new $load[-1];
            $this->dependencies[$className] =& array_push($this->dependencies[$className], $instance);

        }
        return $this->dependencies[$className];
    }

    public function getDependencies($className)
    {
        return $this->dependencies[$className];
    }
}
