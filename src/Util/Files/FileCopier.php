<?php namespace Pauldro\Minicli\Util\Files;


/**
 * Service for Copying files
 * 
 * @property string $errorMsg
 * @property string $lastCopiedFile
 */
class FileCopier {
	protected static $instance;

	public $errorMsg;
    public $lastCopiedFile;


	public static function instance() : FileCopier
    {
		if (empty(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    /**
     * Copy source file to source destination
     * @param  string $fromFile
     * @param  string $toFile
     * @return bool
     */
	public function copy(string $fromFile, string $toFile) : bool
    {
        $this->errorMsg = '';
        $this->lastCopiedFile = '';

		if (file_exists($fromFile) === false) {
			$this->errorMsg = "Source file not found: '$fromFile'";
            return false;
		}
        if (copy($fromFile, $toFile) === false) {
            $this->errorMsg = "Failed copying '$fromFile' to '$toFile'";
            return false;
        }
        $this->lastCopiedFile = $toFile;
        return true;
	}
}
