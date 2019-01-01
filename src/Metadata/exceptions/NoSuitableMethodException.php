<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

class NoSuitableMethodException extends \Consistence\PhpException
{

	/** @var string */
	private $className;

	/** @var string */
	private $propertyName;

	/** @var \Consistence\Sentry\Metadata\SentryAccess */
	private $sentryAccess;

	public function __construct(string $className, string $propertyName, SentryAccess $sentryAccess, ?\Throwable $previous = null)
	{
		parent::__construct(
			sprintf(
				'No suitable method for SentryAccess %s found on %s::$%s',
				$sentryAccess->getName(),
				$className,
				$propertyName
			),
			$previous
		);
		$this->className = $className;
		$this->propertyName = $propertyName;
		$this->sentryAccess = $sentryAccess;
	}

	public function getClassName(): string
	{
		return $this->className;
	}

	public function getPropertyName(): string
	{
		return $this->propertyName;
	}

	public function getSentryAccess(): SentryAccess
	{
		return $this->sentryAccess;
	}

}
