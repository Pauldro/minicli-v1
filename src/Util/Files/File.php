<?php namespace Pauldro\Minicli\Util\Files;
// Base PHP
use SplFileInfo;

class File extends SplFileInfo {
	/**
	 * Return the number of files
	 * @return int
	 */
	public function countFiles() : int 
    {
		if ($this->isFile()) {
			return 1;
		}
		if ($this->isDir() === false) {
			return 0;
		}
		return count(glob($this->getPathname() . '/*', 0));
	}
}