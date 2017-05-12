<?php

declare(strict_types = 1);

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

	/** @var string|null */
	private $sourceClass;

	/**
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $sentryIdentificator
	 * @param string $type
	 * @param boolean $many
	 * @param boolean $nullable
	 * @param string|null $sourceClass
	 */
	public function __construct(SentryIdentificator $sentryIdentificator, string $type, bool $many, bool $nullable, $sourceClass)
	{
		$this->sentryIdentificator = $sentryIdentificator;
		$this->type = $type;
		$this->many = $many;
		$this->nullable = $nullable;
		$this->sourceClass = $sourceClass;
	}

	public function getSentryIdentificator(): SentryIdentificator
	{
		return $this->sentryIdentificator;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function isMany(): bool
	{
		return $this->many;
	}

	public function isNullable(): bool
	{
		return $this->nullable;
	}

	/**
	 * @return string|null
	 */
	public function getSourceClass()
	{
		return $this->sourceClass;
	}

}
