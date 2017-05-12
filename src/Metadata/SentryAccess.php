<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

class SentryAccess extends \Consistence\ObjectPrototype
{

	/** @var string */
	private $name;

	public function __construct(string $name)
	{
		$this->name = $name;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function equals(SentryAccess $to): bool
	{
		return $this->getName() === $to->getName();
	}

}
