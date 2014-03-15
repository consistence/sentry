<?php

namespace Consistence\Sentry\Metadata;

class MethodNotFoundException extends \Consistence\PhpException implements \Consistence\Sentry\Metadata\Exception
{

	/** @var string */
	private $methodName;

	/** @var string */
	private $className;

	/**
	 * @param string $methodName
	 * @param string $className
	 * @param \Exception|null $previous
	 */
	public function __construct($methodName, $className, \Exception $previous = null)
	{
		parent::__construct(sprintf('Method %s not found on %s', $methodName, $className), $previous);
		$this->methodName = $methodName;
		$this->className = $className;
	}

	/**
	 * @return string
	 */
	public function getMethodName()
	{
		return $this->methodName;
	}

	/**
	 * @return string
	 */
	public function getClassName()
	{
		return $this->className;
	}

}
