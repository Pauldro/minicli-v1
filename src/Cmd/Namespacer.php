<?php namespace Pauldro\Minicli\Cmd;
// Base PHP
use ReflectionClass;
use ReflectionException;

/**
 * Ties Controller to a Command
 * 
 * @property string $cmd_namespace Extra Namespace for App (use for separate script drivers)
 */
class Namespacer {
	protected $cmd_namespace = '';
	protected $name;
	protected $nameForFilepath;
	protected $nameForNamespace;
	protected $controllers = [];

	public function __construct($name) {
		$this->name = $name;
		$this->nameForFilepath = str_replace(' ', '/', $this->name);
		$this->nameForNamespace = str_replace(' ', '\\', $this->name);
	}

	/**
	 * Set Extra Namespace
	 * NOTE: used for script drivers and adding 1 extra layer of namespaceing
	 * @param  string $ns
	 * @return void
	 */
	public function setCmdNamespace($ns) : void
	{
		$this->cmd_namespace = $ns;
	}

	/**
	 * Return Name
	 * @return string
	 */
	public function getName() : string
	{
		return $this->name;
	}

	/**
	 * Load Controllers
	 * @param  string $commands_path
	 * @return array
	 */
	public function loadControllers($commands_path) : array
	{
		foreach (glob($commands_path . '/' . $this->nameForFilepath . '/*Controller.php') as $controller_file) {
			$this->loadCommandMap($controller_file);
		}

		return $this->getControllers();
	}

	/**
	 * Return Controllers
	 * @return array
	 */
	public function getControllers() : array
	{
		return $this->controllers;
	}

	/**
	 * Return Controller Class for Command
	 * @param  string $command_name
	 * @return AbstractController|null
	 */
	public function getController($command_name)
	{
		return isset($this->controllers[$command_name]) ? $this->controllers[$command_name] : null;
	}

	/**
	 * Map Controllers for each command
	 * @param  string $controller_file
	 * @return void
	 */
	protected function loadCommandMap($controller_file) : void
	{
		$filename = basename($controller_file);

		$controller_class = str_replace('.php', '', $filename);
		$command_name = strtolower(str_replace('Controller', '', $controller_class));
		
		$namespace = 'App\\Cmd';

		if ($this->cmd_namespace) {
			$namespace .= "\\$this->cmd_namespace";
		}
		
		$full_class_name = sprintf($namespace . '\\%s\\%s', $this->nameForNamespace, $controller_class);

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
