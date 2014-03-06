<?php

namespace Consistence\Sentry\Metadata;

class NoSuitableMethodException extends \Consistence\PhpException implements \Consistence\Sentry\Metadata\Exception
{

	/** @var string */
	private $className;

	/** @var string */
	private $propertyName;

	/** @var \Consistence\Sentry\Metadata\SentryAccess */
	private $sentryAccess;

	/**
	 * @param string $className
	 * @param string $propertyName
	 * @param \Consistence\Sentry\Metadata\SentryAccess $sentryAccess
	 * @param \Exception|null $previous
	 */
	public function __construct($className, $propertyName, SentryAccess $sentryAccess, \Exception $previous = null)
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

	/**
	 * @return \Consistence\Sentry\Metadata\SentryAccess
	 */
	public function getSentryAccess()
	{
		return $this->sentryAccess;
	}

}
