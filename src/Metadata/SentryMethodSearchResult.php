<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

class SentryMethodSearchResult extends \Consistence\ObjectPrototype
{

	/** @var \Consistence\Sentry\Metadata\SentryMethod */
	private $sentryMethod;

	/** @var \Consistence\Sentry\Metadata\PropertyMetadata */
	private $property;

	public function __construct(SentryMethod $sentryMethod, PropertyMetadata $property)
	{
		$this->sentryMethod = $sentryMethod;
		$this->property = $property;
	}

	public function getSentryMethod(): SentryMethod
	{
		return $this->sentryMethod;
	}

	public function getProperty(): PropertyMetadata
	{
		return $this->property;
	}

}
