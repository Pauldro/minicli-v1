<?php namespace Pauldro\Minicli\Cmd;
// Minicli
use Minicli\Command\CommandRegistry as MinicliCommandRegistry;
// Pauldro
use Pauldro\Minicli\Util\StringUtilities;

/**
 * Ties Commands to Namespaces by name
 * 
 * @method AbstactController|null getCallableController($command, $subcommand = "default")
 * 
 * @property string $cmd_namespace Extra Namespace for App (use for separate script drivers)
 */
class CommandRegistry extends MinicliCommandRegistry {
	protected $cmd_namespace;

	/**
     * @return void
     */
    public function autoloadNamespaces()
    {
        foreach (glob($this->getCommandsPath() . '/*', GLOB_ONLYDIR) as $namespace_path) {
            $this->registerNamespace(basename($namespace_path));

            foreach (glob($namespace_path . '/*', GLOB_ONLYDIR) as $subnamespace_path) {
                $this->registerNamespace(basename($namespace_path) . ' ' . basename($subnamespace_path));
            }
        }
    }

	/**
     * @param string $command
     * @param string $subcommand
     * @return AbstractController | null
     */
    public function getCallableControllerFromInput(CommandCall $input)
    {
		$command = strtolower(StringUtilities::camelCase($input->command));
		$subcommand = strtolower(StringUtilities::camelCase($input->subcommand));

		// Check if command namespace exists
        $namespace = $this->getNamespace($command);

		if ($namespace === null) {
			return null;
		}

		$controller = $namespace->getController($subcommand);

		if ($controller !== null) {
			return $controller;
		}

		// check if subcommand namespace exists
		$namespace = $this->getNamespace("$command $subcommand");

		if ($namespace === null) {
			return null;
		}
		$cmd  = isset($input->args[3]) ? $input->args[3] : 'default';
		$cmd  = strtolower(StringUtilities::camelCase($cmd));
		return $namespace->getController($cmd);
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

	public function registerNamespace($command_namespace) : void
	{
		$namespace = new Namespacer($command_namespace);
		$namespace->setCmdNamespace($this->cmd_namespace);
		$namespace->loadControllers($this->getCommandsPath());
		$this->namespaces[strtolower($command_namespace)] = $namespace;
	}
}
