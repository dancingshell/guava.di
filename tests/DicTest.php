<?php
namespace guava;

class DIContainer {

    protected $loaders = array();
    protected $dependencies = array();

    /**
     * @param String $className
     * @param Object $classInstance
     */
    public function __construct($className, &$classInstance)
    {
        if (!isset($this->loaders[$className])) {
            $this->load($className, $classInstance);
        }
    }

    public function load($className, &$classInstance)
    {
        $this->loaders[$className] = $classInstance;
        $reflection = new ReflectionClass($className);
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
            $this->dependencies[$className] =& array_push($class, $instance);
        }

        return $this->dependencies[$className];
    }

    public function getDependencies()
    {
        return $this->dependencies[$className];
    }
}
