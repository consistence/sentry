<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\SentryAccess;

class SentryAccessNotSupportedException extends \Consistence\PhpException implements \Consistence\Sentry\Type\Exception
{

	/** @var \Consistence\Sentry\Metadata\SentryAccess */
	private $sentryAccess;

	/** @var string */
	private $sentryClassName;

	public function __construct(SentryAccess $sentryAccess, string $sentryClassName, \Throwable $previous = null)
	{
		parent::__construct(
			sprintf('SentryAccess %s is not supported by %s', $sentryAccess->getName(), $sentryClassName),
			$previous
		);
		$this->sentryAccess = $sentryAccess;
		$this->sentryClassName = $sentryClassName;
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
