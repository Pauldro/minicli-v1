<?php namespace Pauldro\Minicli\Util\Files;


/**
 * FileFetcher
 * Utility for fetching file contents
 */
class FileFetcher {
    protected static $instance;
    public string $errorMsg;


	public static function instance() : static
    {
		if (empty(static::$instance)) {
			static::$instance = new static();
		}
		return static::$instance;
	}

    /**
     * Return if file exists
     * @param  string $filepath
     * @return bool
     */
    public function exists(string $filepath) : bool
    {
        return file_exists($filepath);
    }

    /**
     * Fetch File Contents
     * @param  string $filepath
     * @return bool|string
     */
    public function fetchContents(string $filepath) : bool|string
    {
        return file_get_contents($filepath);
    }

    /**
     * Fetch File Contents
     * @param  string $filepath
     * @return mixed
     */
    public function fetch(string $filepath) : mixed
    {
        if ($this->exists($filepath) === false) {
            $this->errorMsg = "File not found: $filepath";
        }
        return $this->fetchContents($filepath);
    }

    /**
	 * Delete File
	 * @param  string $filepath
	 * @return bool
	 */
	public function delete(string $filepath) : bool
    {
		return unlink($filepath);
	}

    /**
	 * Return Timestamp of when the file was modified
	 * @param  string $filepath
	 * @return int
	 */
	public function modified($filepath) : bool|int
    {
		if ($this->exists($filepath) === false) {
			return 0;
		}
		return filemtime($filepath);
	}

    /**
	 * Convert File to UTF-8 encoding
	 * @param  string $filepath
	 * @return bool
	 */
	public function convertToUtf8($filepath) : bool
    {
		if ($this->exists($filepath) === false) {
            $this->errorMsg = "File not found: $filepath";
			return false;
		}

		$content = $this->fetchContents($filepath);
		# detect original encoding
		$original_encoding = mb_detect_encoding($content, "UTF-8, ISO-8859-1, ISO-8859-15", true);
		# now convert
		if ($original_encoding != 'UTF-8') {
			$content = mb_convert_encoding($content, 'UTF-8', $original_encoding);
		}
		$results = file_put_contents($filepath, $content);
		return boolval($results);
	}
}
