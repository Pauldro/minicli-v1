<?php namespace Pauldro\Minicli\Cmd;
// Base PHP
use ReflectionClass;
use ReflectionException;

/**
 * Ties Controller to a Command
 * 
 * @property string $app_namespace Extra Namespace for App (use for separate script drivers)
 */
class Namespacer {
	protected $app_namespace = '';
	protected $name;
	protected $controllers = [];

	public function __construct($name) {
		$this->name = $name;
	}

	/**
	 * Set Extra Namespace
	 * NOTE: used for script drivers and adding 1 extra layer of namespaceing
	 * @param  string $ns
	 * @return void
	 */
	public function setAppNamespace($ns) {
		$this->app_namespace = $ns;
	}

	/**
	 * Return Name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Load Controllers
	 * @param  string $commands_path
	 * @return array
	 */
	public function loadControllers($commands_path) {
		foreach (glob($commands_path . '/' . $this->getName() . '/*Controller.php') as $controller_file) {
			$this->loadCommandMap($controller_file);
		}

		return $this->getControllers();
	}

	/**
	 * Return Controllers
	 * @return array
	 */
	public function getControllers() {
		return $this->controllers;
	}

	/**
	 * Return Controller Class for Command
	 * @param  string $command_name
	 * @return string
	 */
	public function getController($command_name) {
		return isset($this->controllers[$command_name]) ? $this->controllers[$command_name] : null;
	}

	/**
	 * Map Controllers for each command
	 * @param  string $controller_file
	 * @return void
	 */
	protected function loadCommandMap($controller_file) {
		$filename = basename($controller_file);

		$controller_class = str_replace('.php', '', $filename);
		$command_name = strtolower(str_replace('Controller', '', $controller_class));
		
		$namespace = 'App\\Cmd';

		if ($this->app_namespace) {
			$namespace .= "\\$this->app_namespace";
		}
		
		$full_class_name = sprintf($namespace . '\\%s\\%s', $this->getName(), $controller_class);

		try {
			$reflector = new ReflectionClass($full_class_name);
		} catch (ReflectionException $e) {
			return;
		}
		
		if ($reflector->isAbstract()) {
			return;
		}

		/** @var AbstractController $controller */
		$controller = new $full_class_name();
		$this->controllers[$command_name] = $controller;
	}
}
