<?php namespace Pauldro\Minicli\Util\Files;

/**
 * FileWriter
 * Writes Files
 *
 * @property string $errorMsg
 * @property string $lastWrittenFile
 */
class FileWriter {
    private static $instance;
    public  $errorMsg;
	public  $lastWrittenFile;

	public static function instance() : FileWriter
	{
		if (empty(self::$instance)) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * Write to File
	 * @param  string        $filepath filepath
	 * @param  mixed|string  $content
	 * @return bool
	 */
	public function write($filepath, $content) : bool
    {
		if (boolval(file_put_contents($filepath, $content))) {
			$this->lastWrittenFile = $filepath;
			return true;
		}
		$this->errorMsg = "Failed to Write File: '$filepath'";
		return false;
	}

}
