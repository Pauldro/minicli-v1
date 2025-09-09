<?php namespace Pauldro\Minicli\Cmd\Help;
// PHP Core
use ReflectionClass;
// Pauldro Minicli
use Pauldro\Minicli\Cmd\AbstractController as ParentController;
use Pauldro\Minicli\Util\StringUtilities as Strings;


/**
 * AbstractController
 * Handles Displaying the Help Screen for a Command
 *
 * @property array $commandMap Array of Map Commands
 */
abstract class AbstractController extends ParentController {
	const COMMAND = '';
	const DESCRIPTION = '';
	const COMMAND_DEFINITIONS = [];
	const OPTIONS = [];
	const OPTIONS_DEFINITIONS = [];
	const OPTIONS_DEFINITIONS_OVERRIDE = [];
	const SUBCOMMANDS = [];
	const NOTES = [];
	const INTRO_DELIMITER = '/////////////////////////////////////////////////////////';
	const REQUIRED_PARAMS = [];

/* =============================================================
	Minicli Controller Contracts
============================================================= */
	public function handle() : void
    {
		$this->display();
	}

	/**
     * The list of parameters required by the command.
     *
     * @return array<int, string>
     */
    public function required() : array
    {
        return [];
    }

/* =============================================================
	Display Printing
============================================================= */
	/**
	 * Display Command
	 * @return void
	 */
	protected function display() : void
    {
		$this->displayUsage();
		$this->displayOptions();
		$this->displayRequiredParams();
		$this->displayHelp();
		$this->displaySubcommands();
		$this->displayNotes();

		$this->printer->newline();
		$this->printer->newline();
	}

	/**
	 * Display Command Usage
	 * @return void
	 */
	protected function displayUsage() : void
    {
		$printer = $this->printer;
		$printer->line($printer->info('Usage:'));
		$script = $this->app->getSignature();
		$printer->line(sprintf('%s%s%s', $script, $printer->spaces(1), static::COMMAND . ' [options]'));
		$printer->newline();
		return;
	}

	/**
	 * Display Command Options
	 * @return void
	 */
	protected function displayOptions() : void
    {
		$printer = $this->printer;
		$optLength = $this->getLongestOptExampleLength() + 4;
		$printer->info('Options:');

		foreach (static::OPTIONS as $option => $example) {
			$printer->line(sprintf('%s%s%s', $printer->spaces(2), Strings::pad($example, $optLength), $this->getOptDefinition($option)));
		}
		return;
	}

	/**
	 * Display Required Command Parameters
	 * @return void
	 */
	protected function displayRequiredParams() : void
    {
		if (empty(static::REQUIRED_PARAMS)) {
			return;
		}

		$printer = $this->printer;
		$optLength = $this->getLongestOptExampleLength() + 4;
		$printer->info('Required:');

		foreach (static::REQUIRED_PARAMS as $option) {
			if (array_key_exists($option, static::OPTIONS) === false) {
				continue;
			}
			
			$example = Strings::pad($option, $optLength);
			$description = $this->getOptDefinition($option);
			$printer->line(sprintf('%s%s%s', $printer->spaces(2), $example, $description));
		}
		return;
	}

	/**
	 * Display Command Help
	 * @return void
	 */
	protected function displayHelp() : void
    {
		$printer = $this->printer;
		$printer->info('Help:');
		$printer->line(sprintf('%s%s', $printer->spaces(2), static::DESCRIPTION));
		return;
	}

	/**
	 * Display Notes
	 * @return void
	 */
	protected function displayNotes() : void
    {
		$printer = $this->printer;

		if (empty(static::NOTES) === false) {
			$printer->info('Notes:');
		}

		foreach (static::NOTES as $line) {
			$printer->line(sprintf('%s%s%s', $printer->spaces(2), ' ', $line));
		}
		return;
	}

	/**
	 * Display Subcommands
	 * @return void
	 */
	protected function displaySubcommands() : void
    {
		$printer = $this->printer;

        if (empty(static::SUBCOMMANDS)) {
            return;
        }
		$printer->info('See Also:');
		

		foreach (static::SUBCOMMANDS as $cmd) {
			$printer->line(sprintf('%s%s%s%s%s', $printer->spaces(2), 'help ', static::COMMAND, ' ', $cmd));
		}
		return;
	}

	/**
	 * Display Subcommand
	 * @return bool
	 */
	protected function displaySubcommand() : void
    {
		if (in_array($this->input->lastArg(), static::SUBCOMMANDS) === false) {
			return;
		}
		$handler = $this->getSubcommandController($this->input->subcommand, $this->input->lastArg());
		if (empty($handler)) {
			return;
		}
		$handler->boot($this->app);
		$handler->handle();
		return;
	}

/* =============================================================
	Supplemental
============================================================= */
	/**
	 * Return String Length of Longest Command
	 * @return int
	 */
	protected function getLongestOptLength() : int
    {
		return Strings::longestStrlen(array_keys(static::OPTIONS_DEFINITIONS)); 
	}

	/**
	 * Return String Length of Longest Command
	 * @return int
	 */
	protected function getLongestOptExampleLength() : int
    {
		return Strings::longestStrlen(array_values(static::OPTIONS));
	}

	/**
	 * Return Argument Defination
	 * @param  string $opt Option, Argument (param|flag)
	 * @return string
	 */
	protected function getOptDefinition($opt) : string
    {
		if (array_key_exists($opt, static::OPTIONS_DEFINITIONS_OVERRIDE)) {
			return static::OPTIONS_DEFINITIONS_OVERRIDE[$opt];
		}
		if (array_key_exists($opt, static::OPTIONS_DEFINITIONS) === false) {
			return '';
		}
		return static::OPTIONS_DEFINITIONS[$opt];
	}

	protected function getSubcommandController(string $command, string $subcommand) : ParentController|false 
	{
		$reflector = new ReflectionClass(get_class($this));
		$ns = $reflector->getNamespaceName() . '\\' . ucfirst($command) . '\\';
		$class = $ns . ucfirst($subcommand) . 'Controller';

		if (class_exists($class) === false) {
			return $this->error("Controller not found: $class");
		}
		return new $class();
	}
}
