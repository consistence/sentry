<?php

declare(strict_types = 1);

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
	 * @param \Throwable|null $previous
	 */
	public function __construct(string $className, $propertyName, \Throwable $previous = null)
	{
		$message = sprintf('Property %s not found on class %s', $propertyName, $className);
		parent::__construct($message, $previous);
		$this->className = $className;
		$this->propertyName = $propertyName;
	}

	public function getClassName(): string
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
