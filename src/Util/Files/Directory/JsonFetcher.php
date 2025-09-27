<?php namespace Pauldro\Minicli\Util\Files\Directory;
// Pauldro Minicli
use Pauldro\Minicli\Util\Files\JsonFetcher as Fetcher;

/**
 * JsonFetcher
 * Wrapper for fetching JSON files from a single directory
 * 
 * @property Fetcher $fetcher
 * @property string  $dir
 * @property string  $errorMsg
 */
class JsonFetcher extends FileFetcher {
    protected $fetcher;

	public function __construct(string $dir) {
		parent::__construct($dir);
		$this->fetcher = Fetcher::instance();
	}

    /**
	 * Return Filepath
	 * @param  string $filename
	 * @return string
	 */
	public function filepath(string $filename) : string
    {
        $filename = rtrim($filename, '.json') . '.json';
		return parent::filepath($filename);
	}
}
