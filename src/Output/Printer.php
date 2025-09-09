<?php namespace Pauldro\Minicli\Output;
// Minicli Library
use Minicli\Output\Filter;
use Minicli\Output\OutputHandler;

/**
 * Printer
 * Handles CLI Output by Extending Mincli's Output Handler
 */
class Printer extends OutputHandler {
	private static $instance;

	/**
	 * Return Instance
	 * @return self
	 */
	public static function instance() : Printer
	{
		if (empty(self::$instance)) {
			self::$instance = new self();
			self::$instance->registerFilter(new Filter\ColorOutputFilter());
		}
		return self::$instance;
	}

	/**
	 * Print Spaces
	 * @param  int   $spaces
	 * @return string
	 */
	public function spaces($spaces = 0) : string
	{
		return str_pad('', $spaces , ' ');
	}

	/**
	 * Displays content using the "default" style
	 * @param  string $content
	 * @param  bool $alt Whether or not to use the inverted style ("alt")
	 * @return void
	 */
	public function line($content, $alt = false) : void
	{
		$this->out($content, $alt ? "alt" : "default");
		$this->newline();
	}
}
