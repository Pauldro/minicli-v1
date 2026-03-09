<?php namespace Pauldro\Minicli\Cmd;
// Base PHP
use ReflectionClass;


class CommandReflectionClass extends ReflectionClass {

    public function getControllerName() : string
    {
        return str_replace('Controller', '', $this->getShortName());
    }

    public function getShortNamespaceName(int $levels = 1) : string
    {
        $segments = explode('\\', $this->getNamespaceName());
        return implode('\\', array_slice($segments, -$levels));
    }

    public function hasOtherControllersInCmdDir() : bool
    {
        return boolval($this->countOtherControllersCmdDir());
    } 

    public function countOtherControllersCmdDir() : int
    {
        return count(glob(dirname($this->getFileName()) . "/*Controller.php")) - 1;
    }
}