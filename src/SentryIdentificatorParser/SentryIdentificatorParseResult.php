<?php

namespace Consistence\Sentry\SentryIdentificatorParser;

use Consistence\Sentry\Metadata\SentryIdentificator;

class SentryIdentificatorParseResult extends \Consistence\ObjectPrototype
{

	/** @var \Consistence\Sentry\Metadata\SentryIdentificator */
	private $sentryIdentificator;

	/** @var string */
	private $type;

	/** @var boolean */
	private $many;

	/** @var boolean */
	private $nullable;

	/**
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $sentryIdentificator
	 * @param string $type
	 * @param boolean $many
	 * @param boolean $nullable
	 */
	public function __construct(SentryIdentificator $sentryIdentificator, $type, $many, $nullable)
	{
		$this->sentryIdentificator = $sentryIdentificator;
		$this->type = $type;
		$this->many = $many;
		$this->nullable = $nullable;
	}

	/**
	 * @return \Consistence\Sentry\Metadata\SentryIdentificator
	 */
	public function getSentryIdentificator()
	{
		return $this->sentryIdentificator;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return boolean
	 */
	public function isMany()
	{
		return $this->many;
	}

	/**
	 * @return boolean
	 */
	public function isNullable()
	{
		return $this->nullable;
	}

}
