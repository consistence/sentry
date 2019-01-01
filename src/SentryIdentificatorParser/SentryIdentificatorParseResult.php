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

	/** @var bool */
	private $many;

	/** @var bool */
	private $nullable;

	/** @var string|null */
	private $sourceClass;

	public function __construct(SentryIdentificator $sentryIdentificator, string $type, bool $many, bool $nullable, ?string $sourceClass)
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

	public function getSourceClass(): ?string
	{
		return $this->sourceClass;
	}

}
