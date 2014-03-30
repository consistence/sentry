<?php

namespace Consistence\Sentry\Metadata;

class PropertyNotFoundException extends \Consistence\PhpException implements \Consistence\Sentry\Metadata\Exception
{

	/** @var string */
	private $className;

	/** @var string */
	private $propertyName;

	/**
	 * @param string $className
	 * @param string|null $propertyName
	 * @param \Exception|null $previous
	 */
	public function __construct($className, $propertyName, \Exception $previous = null)
	{
		$message = sprintf('Property %s not found on class %s', $propertyName, $className);
		parent::__construct($message, $previous);
		$this->className = $className;
		$this->propertyName = $propertyName;
	}

	/**
	 * @return string
	 */
	public function getClassName()
	{
		return $this->className;
	}

	/**
	 * @return string|null
	 */
	public function getPropertyName()
	{
		return $this->propertyName;
	}

}
