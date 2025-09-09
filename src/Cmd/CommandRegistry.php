<?php namespace Pauldro\Minicli\Cmd;
// Minicli
use Minicli\Command\CommandRegistry as MinicliCommandRegistry;

/**
 * Ties Commands to Namespaces by name
 * 
 * @property string $cmd_namespace Extra Namespace for App (use for separate script drivers)
 */
class CommandRegistry extends MinicliCommandRegistry {
	protected $cmd_namespace;

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

	public function registerNamespace($command_namespace) : void
	{
		$namespace = new Namespacer($command_namespace);
		$namespace->setCmdNamespace($this->cmd_namespace);
		$namespace->loadControllers($this->getCommandsPath());
		$this->namespaces[strtolower($command_namespace)] = $namespace;
	}
}
