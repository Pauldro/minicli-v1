<?php namespace Pauldro\Minicli\Cmd;
// Pauldro Minicli
use Pauldro\Minicli\Util\SimpleArray;

/**
 * Container for Reflection Classes
 */
class CommandReflectionClassFactory extends SimpleArray {
    protected static $instance;

    private function __construct() {
        
    }

    public static function instance() : CommandReflectionClassFactory
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param object|string $objectOrClass Either a `string` containing the name of the class to reflect, or an `object`.
     */
    public static function fetch($objectOrClass) : CommandReflectionClass
    {
        $list = self::instance();
        $className = is_object($objectOrClass) ? get_class($objectOrClass) : $objectOrClass;

        if ($list->has($className)) {
            return $list->get($className);
        }
        $reflector = new CommandReflectionClass($className);
        $list->set($className, $reflector);
        return $reflector; 
    }
}