<?php

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

	/**
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param \Consistence\Sentry\Metadata\SentryAccess $sentryAccess
	 * @param string $sentryClassName
	 * @param \Exception|null $previous
	 */
	public function __construct(PropertyMetadata $property, SentryAccess $sentryAccess, $sentryClassName, \Exception $previous = null)
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

	/**
	 * @return \Consistence\Sentry\Metadata\PropertyMetadata
	 */
	public function getProperty()
	{
		return $this->property;
	}

	/**
	 * @return \Consistence\Sentry\Metadata\SentryAccess
	 */
	public function getSentryAccess()
	{
		return $this->sentryAccess;
	}

	/**
	 * @return string
	 */
	public function getSentryClassName()
	{
		return $this->sentryClassName;
	}

}
