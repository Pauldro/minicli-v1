<?php namespace Pauldro\Minicli\Cmd;
// Minicli
use Minicli\Command\CommandRegistry as MinicliCommandRegistry;

/**
 * Ties Commands to Namespaces by name
 * 
 * @property string $app_namespace Extra Namespace for App (use for separate script drivers)
 */
class CommandRegistry extends MinicliCommandRegistry {
	/**
	 * Set Extra Namespace
	 * NOTE: used for script drivers and adding 1 extra layer of namespaceing
	 * @param  string $ns
	 * @return void
	 */
	public function setAppNamespace($ns) {
		$this->app_namespace = $ns;
	}

	public function registerNamespace($command_namespace) {
		$namespace = new Namespacer($command_namespace);
		$namespace->setAppNamespace($this->app_namespace);
		$namespace->loadControllers($this->getCommandsPath());
		$this->namespaces[strtolower($command_namespace)] = $namespace;
	}
}
