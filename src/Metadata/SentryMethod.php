<?php

namespace Consistence\Sentry\Metadata;

use Consistence\Type\Type;

class SentryMethod extends \Consistence\ObjectPrototype
{

	/** @var \Consistence\Sentry\Metadata\SentryAccess */
	private $sentryAccess;

	/** @var string */
	private $methodName;

	/** @var \Consistence\Sentry\Metadata\Visibility */
	private $methodVisibility;

	/**
	 * @param \Consistence\Sentry\Metadata\SentryAccess $sentryAccess
	 * @param string $methodName
	 * @param \Consistence\Sentry\Metadata\Visibility $methodVisibility
	 */
	public function __construct(
		SentryAccess $sentryAccess,
		$methodName,
		Visibility $methodVisibility
	)
	{
		Type::checkType($methodName, 'string');
		$this->sentryAccess = $sentryAccess;
		$this->methodName = $methodName;
		$this->methodVisibility = $methodVisibility;
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
	public function getMethodName()
	{
		return $this->methodName;
	}

	/**
	 * @return \Consistence\Sentry\Metadata\Visibility
	 */
	public function getMethodVisibility()
	{
		return $this->methodVisibility;
	}

}
