<?php namespace Pauldro\Minicli\Exceptions;
// Base PHP
use Exception;
// Dotenv
use Dotenv\Exception\ValidationException;

class MissingEnvVarsException extends Exception {
    private $vars = [];
    private $filepath = '';

    /**
     * Parse Missing Variables
     * @param  ValidationException $e
     * @return void
     */
    public function parseVarsFromValidationException(ValidationException $e) : void
    {
        $pieces = explode(':', $e->getMessage());
        $msg = '.env required failure: ' . trim($pieces[1]);
        $vars = [];

        foreach (explode(',', $msg) as $part) {
            $var = rtrim($part, ' is missing');
            $var = trim($var);
            $vars[] = $var;
        }
        $this->vars = $vars;
    }

    /**
     * Set .env filepath
     * @param  string $filepath
     * @return void
     */
    public function setFilepath($filepath) : void 
    {
        $this->filepath = $filepath;
    }

    /**
     * Generate Error Message
     * @return void
     */
    public function generateMessage() : void
    {
        $msg = '.env missing variables: ' . implode(", ", $this->vars);

        if ($this->filepath) {
            $msg .= " (.env file: $this->filepath)";
        }
        $this->message = $msg;
    }
}
