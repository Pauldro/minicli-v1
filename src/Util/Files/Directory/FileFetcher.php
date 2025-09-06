<?php namespace Pauldro\Minicli\Util\Files\Directory;
// Base PHP
use Exception;
// Pauldro Minicli
use Pauldro\Minicli\Util\Files\FileFetcher as Fetcher;

/**
 * FileFetcher
 *
 * Wrapper for fetching files from a single directory
 * @property Fetcher $fetcher
 */
class FileFetcher {
    protected string $dir;
    protected $fetcher;
    public string $errorMsg;

    public function __construct(string $dir) {
        if (is_dir($dir) === false) {
			throw new Exception("Directory not found: '$dir'");
		}
		$this->dir = $dir;
        $this->fetcher = Fetcher::instance();
    }

    /**
	 * Return Filepath
	 * @param  string $filename
	 * @return string
	 */
	public function filepath(string $filename) : string
    {
		return rtrim($this->dir, '/') . '/' . $filename;
	}

    /**
     * Return if file exists
     * @param  string $filename
     * @return bool
     */
    public function exists(string $filename) : bool
    {
        return $this->fetcher->exists($this->filepath($filename));
    }

    /**
     * Return File Contents
     * @param  string $filename
     * @return array|bool|string
     */
    public function fetch(string $filename) : array|bool|string
    {
        $data = $this->fetcher->fetch($this->filepath($filename));

        if ($data === false) {
            $this->errorMsg = $this->fetcher->errorMsg;
        }
        return $data;
    }

    /**
     * Delete File
     * @param  string $filename
     * @return bool
     */
    public function delete(string $filename) : bool
    {
        return $this->fetcher->delete($this->filepath($filename));
    }

    /**
     * Return File Modified timestamp
     * @param  string $filename
     * @return int
     */
    public function modified(string $filename) : int
    {
        return $this->fetcher->modified($this->filepath($filename));
    }

    /**
     * Convert File to UTF-8
     * @param  string $filename
     * @return bool
     */
    public function convertToUtf8(string $filename) : bool
    {
        if ($this->fetcher->convertToUtf8($this->filepath($filename)) === false) {
            $this->errorMsg = $this->fetcher->errorMsg;
            return false;
        }
        return true;
    }
}
