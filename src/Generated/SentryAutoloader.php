<?php

namespace Consistence\Sentry\Generated;

use Closure;

class SentryAutoloader extends \Consistence\ObjectPrototype
{

	/** @var \Consistence\Sentry\Generated\SentryGenerator */
	private $generator;

	/** @var string */
	private $classMapTargetFile;

	/** @var string[] className => fileName */
	private $classMap;

	/**
	 * @param \Consistence\Sentry\Generated\SentryGenerator $generator
	 * @param string $classMapTargetFile
	 */
	public function __construct(SentryGenerator $generator, $classMapTargetFile)
	{
		$this->generator = $generator;
		$this->classMapTargetFile = $classMapTargetFile;
		$this->classMap = [];
	}

	/**
	 * @see self::register() SentryAutoloader must have a class map first in order to work properly
	 *
	 * @codeCoverageIgnore changes global state via loading classes
	 *
	 * @param string $type
	 * @return boolean was loaded?
	 */
	public function tryLoad($type)
	{
		if (!isset($this->classMap[$type])) {
			return false;
		}

		$file = $this->classMap[$type];
		call_user_func(Closure::bind(function () use ($file) {
			require $file;
		}, null));

		return true;
	}

	/**
	 * Regenerate all classes and create new class map
	 */
	public function rebuild()
	{
		$this->classMap = $this->generator->generateAll();
		$this->saveClassMap();
	}

	/**
	 * @codeCoverageIgnore changes global state via registering an autoloader
	 *
	 * @param boolean $prepend should be set to true in most cases in order to load the generated classes
	 */
	public function register($prepend = true)
	{
		if ($this->isClassMapReady()) {
			$this->loadClassMap();
		} else {
			$this->rebuild();
		}
		spl_autoload_register(function ($type) {
			return $this->tryLoad($type);
		}, true, $prepend);
	}

	/**
	 * @return boolean
	 */
	public function isClassMapReady()
	{
		return is_file($this->classMapTargetFile);
	}

	private function saveClassMap()
	{
		$fileContent = sprintf('<?php return %s;', var_export($this->classMap, true));
		file_put_contents($this->classMapTargetFile, $fileContent);
	}

	/**
	 * @codeCoverageIgnore private method called only from global dependent code
	 */
	private function loadClassMap()
	{
		$this->classMap = require $this->classMapTargetFile;
	}

}
