<?php

namespace Consistence\Sentry\SentryIdentificatorParser;

use Consistence\Sentry\Metadata\SentryIdentificator;

class PatternDoesNotMatchException extends \Consistence\PhpException implements \Consistence\Sentry\SentryIdentificatorParser\Exception
{

	/** @var \Consistence\Sentry\Metadata\SentryIdentificator */
	private $sentryIdentificator;

	public function __construct(SentryIdentificator $sentryIdentificator, \Exception $previous = null)
	{
		$message = 'Pattern does not match identificator ' . $sentryIdentificator->getId();
		parent::__construct($message, $previous);
		$this->sentryIdentificator = $sentryIdentificator;
	}

	/**
	 * @return \Consistence\Sentry\Metadata\SentryIdentificator
	 */
	public function getSentryIdentificator()
	{
		return $this->sentryIdentificator;
	}

}
