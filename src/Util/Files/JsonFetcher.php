<?php namespace Pauldro\Minicli\Util\Files;

/**
 * JsonFetcher
 * Utility for fetching JSON file contents
 */
class JsonFetcher extends FileFetcher {
    protected static $instance;

    /**
     * Fetch File Contents
     * @param  string $filepath
     * @return array
     */
    public function fetch(string $filepath) : mixed {
       if ($this->exists($filepath) === false) {
			$this->errorMsg = 'File not found: ' . $filepath;
			return [];
		}
        $this->convertToUtf8($filepath) ;
		$json = json_decode(file_get_contents($filepath), true);

        if (empty($json)) {
			$this->errorMsg = "The '$filepath' JSON contains errors, JSON ERROR: ". json_last_error();
		}
		return $json;
    }
}
