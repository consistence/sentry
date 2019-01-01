<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

class MethodNotFoundForPropertyException extends \Consistence\PhpException
{

	/** @var string */
	private $methodName;

	/** @var string */
	private $className;

	/** @var string */
	private $propertyName;

	public function __construct(
		string $methodName,
		string $className,
		string $propertyName,
		?\Throwable $previous = null
	)
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

	public function getMethodName(): string
	{
		return $this->methodName;
	}

	public function getClassName(): string
	{
		return $this->className;
	}

	public function getPropertyName(): string
	{
		return $this->propertyName;
	}

}
