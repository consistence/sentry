<?php

namespace Consistence\Sentry\Factory;

use Consistence\Sentry\Metadata\SentryIdentificator;

class NoSentryForIdentificatorException extends \Consistence\PhpException implements \Consistence\Sentry\Factory\Exception
{

	/** @var \Consistence\Sentry\Metadata\SentryIdentificator */
	private $sentryIdentificator;

	public function __construct(SentryIdentificator $sentryIdentificator, \Exception $previous = null)
	{
		$message = 'No Sentry can be created for identificator: ' . $sentryIdentificator->getId();
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
