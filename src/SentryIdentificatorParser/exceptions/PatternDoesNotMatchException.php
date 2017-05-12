<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SentryIdentificatorParser;

use Consistence\Sentry\Metadata\SentryIdentificator;

class PatternDoesNotMatchException extends \Consistence\PhpException
{

	/** @var \Consistence\Sentry\Metadata\SentryIdentificator */
	private $sentryIdentificator;

	public function __construct(SentryIdentificator $sentryIdentificator, \Throwable $previous = null)
	{
		$message = 'Pattern does not match identificator ' . $sentryIdentificator->getId();
		parent::__construct($message, $previous);
		$this->sentryIdentificator = $sentryIdentificator;
	}

	public function getSentryIdentificator(): SentryIdentificator
	{
		return $this->sentryIdentificator;
	}

}
