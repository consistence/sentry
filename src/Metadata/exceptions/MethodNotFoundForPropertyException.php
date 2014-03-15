<?php

namespace Consistence\Sentry\Metadata;

class MethodNotFoundForPropertyException extends \Consistence\PhpException implements \Consistence\Sentry\Metadata\Exception
{

	/** @var string */
	private $methodName;

	/** @var string */
	private $className;

	/** @var string */
	private $propertyName;

	/**
	 * @param string $methodName
	 * @param string $className
	 * @param string $propertyName
	 * @param \Exception|null $previous
	 */
	public function __construct($methodName, $className, $propertyName, \Exception $previous = null)
	{
		parent::__construct(sprintf(
			'Method %s not found on %s::$%s',
			$methodName,
			$className,
			$propertyName
		), $previous);
		$this->methodName = $methodName;
		$this->className = $className;
		$this->propertyName = $propertyName;
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

	/**
	 * @return string
	 */
	public function getPropertyName()
	{
		return $this->propertyName;
	}

}
