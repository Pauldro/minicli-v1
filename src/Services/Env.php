<?php namespace Pauldro\Minicli\Services;
use Exception;
// DotEnv
use Dotenv\Dotenv;
use Dotenv\Exception\ValidationException;
// Minicli
use Minicli\App as MinicliApp;
use Minicli\ServiceInterface;
// Lib
use Pauldro\Minicli\App\App;
use Pauldro\Minicli\Exceptions\MissingEnvVarsException;


/**
 * Dotenv
 * Wrapper for Dotenv for environment variables
 */
class Env implements ServiceInterface {
    const REQUIRED = [];
    protected $dir;
    protected $filepath;
    protected $env;

    /**
     * load
     * @param  App  $app
     * @throws Exception
     * @return void
     */
    public function load(MinicliApp $app) : void
    {
        $this->dir = $app->config->base_path;
        
        try {
            $dotenv = Dotenv::createImmutable($this->dir);
            $dotenv->load();
        } catch (Exception $e) {
            throw new Exception("Unable to load app .env");
        }
        $this->filepath = $this->dir . '.env';
        $this->env = $dotenv;
        $this->env->required(static::REQUIRED);
    }

    /**
     * Return if required variables are set
     * @param  array $vars
     * @throws MissingEnvVarsException
     * @return bool
     */
    public function required(array $vars) : bool
    {
        try {
            $this->env->required($vars);
        } catch (ValidationException $e) {
            $exception = new MissingEnvVarsException($e->getMessage());
            $exception->parseVarsFromValidationException($e);
            $exception->setFilepath($this->filepath);
            $exception->generateMessage();
            throw $exception;
        }
        return true;
    }

    /**
     * Return if variable is set
     * @param  string $var
     * @return bool
     */
    public function exists(string $var) : bool
    {
        try {
            $this->env->required($var);
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    /**
     * Return value
     * @param  string $var
     * @return string|null
     */
    public function get(string $var) : string
    {
        if ($this->exists($var) === false) {
            return '';
        }
        return $_ENV[$var];
    }

    /**
     * Return value as a boolean
     * @param  string $var
     * @return bool
     */
    public function getBool(string $var) : bool
    {
        if ($this->exists($var) === false) {
            return false;
        }
        return $_ENV[$var] == 'true';
    }
}