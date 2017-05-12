<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryAccess;

class SentryAccessNotSupportedForPropertyException extends \Consistence\PhpException implements \Consistence\Sentry\Type\Exception
{

	/** @var \Consistence\Sentry\Metadata\PropertyMetadata */
	private $property;

	/** @var \Consistence\Sentry\Metadata\SentryAccess */
	private $sentryAccess;

	/** @var string */
	private $sentryClassName;

	public function __construct(
		PropertyMetadata $property,
		SentryAccess $sentryAccess,
		string $sentryClassName,
		\Throwable $previous = null
	)
	{
		parent::__construct(sprintf(
			'SentryAccess %s defined on %s::$%s is not supported by %s',
			$sentryAccess->getName(),
			$property->getClassName(),
			$property->getName(),
			$sentryClassName
		), $previous);
		$this->property = $property;
		$this->sentryAccess = $sentryAccess;
		$this->sentryClassName = $sentryClassName;
	}

	public function getProperty(): PropertyMetadata
	{
		return $this->property;
	}

	public function getSentryAccess(): SentryAccess
	{
		return $this->sentryAccess;
	}

	public function getSentryClassName(): string
	{
		return $this->sentryClassName;
	}

}
