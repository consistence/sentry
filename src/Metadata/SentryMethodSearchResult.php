<?php

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

	/**
	 * @return \Consistence\Sentry\Metadata\SentryMethod
	 */
	public function getSentryMethod()
	{
		return $this->sentryMethod;
	}

	/**
	 * @return \Consistence\Sentry\Metadata\PropertyMetadata
	 */
	public function getProperty()
	{
		return $this->property;
	}

}
