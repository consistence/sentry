<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

class PropertyNotFoundException extends \Consistence\PhpException
{

	/** @var string */
	private $className;

	/** @var string|null */
	private $propertyName;

	public function __construct(string $className, ?string $propertyName, ?\Throwable $previous = null)
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

	public function getPropertyName(): ?string
	{
		return $this->propertyName;
	}

}
