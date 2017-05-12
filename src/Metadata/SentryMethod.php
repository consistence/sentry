<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

class SentryMethod extends \Consistence\ObjectPrototype
{

	/** @var \Consistence\Sentry\Metadata\SentryAccess */
	private $sentryAccess;

	/** @var string */
	private $methodName;

	/** @var \Consistence\Sentry\Metadata\Visibility */
	private $methodVisibility;

	public function __construct(
		SentryAccess $sentryAccess,
		string $methodName,
		Visibility $methodVisibility
	)
	{
		$this->sentryAccess = $sentryAccess;
		$this->methodName = $methodName;
		$this->methodVisibility = $methodVisibility;
	}

	public function getSentryAccess(): SentryAccess
	{
		return $this->sentryAccess;
	}

	public function getMethodName(): string
	{
		return $this->methodName;
	}

	public function getMethodVisibility(): Visibility
	{
		return $this->methodVisibility;
	}

}
