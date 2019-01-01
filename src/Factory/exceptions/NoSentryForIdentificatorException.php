<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Factory;

use Consistence\Sentry\Metadata\SentryIdentificator;

class NoSentryForIdentificatorException extends \Consistence\PhpException
{

	/** @var \Consistence\Sentry\Metadata\SentryIdentificator */
	private $sentryIdentificator;

	public function __construct(SentryIdentificator $sentryIdentificator, ?\Throwable $previous = null)
	{
		$message = 'No Sentry can be created for identificator: ' . $sentryIdentificator->getId();
		parent::__construct($message, $previous);
		$this->sentryIdentificator = $sentryIdentificator;
	}

	public function getSentryIdentificator(): SentryIdentificator
	{
		return $this->sentryIdentificator;
	}

}
