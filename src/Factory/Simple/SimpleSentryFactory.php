<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Factory\Simple;

use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\SentryIdentificatorParser\SentryIdentificatorParser;
use Consistence\Sentry\Type\CollectionType;
use Consistence\Sentry\Type\Sentry;
use Consistence\Sentry\Type\SimpleType;

class SimpleSentryFactory extends \Consistence\ObjectPrototype implements \Consistence\Sentry\Factory\SentryFactory
{

	/** @var \Consistence\Sentry\SentryIdentificatorParser\SentryIdentificatorParser */
	private $sentryIdentificatorParser;

	/** @var \Consistence\Sentry\Type\Sentry[] format: sentry identificator -> \Consistence\Sentry\Type\Sentry */
	private $sentries;

	public function __construct(SentryIdentificatorParser $sentryIdentificatorParser)
	{
		$this->sentryIdentificatorParser = $sentryIdentificatorParser;
		$this->sentries = [];
	}

	public function getSentry(SentryIdentificator $sentryIdentificator): Sentry
	{
		if (!isset($this->sentries[$sentryIdentificator->getId()])) {
			$this->sentries[$sentryIdentificator->getId()] = $this->createSentry($sentryIdentificator);
		}

		return $this->sentries[$sentryIdentificator->getId()];
	}

	private function createSentry(SentryIdentificator $sentryIdentificator): Sentry
	{
		try {
			$sentryIdentificatorParseResult = $this->sentryIdentificatorParser->parse($sentryIdentificator);
		} catch (\Consistence\Sentry\SentryIdentificatorParser\PatternDoesNotMatchException $e) {
			throw new \Consistence\Sentry\Factory\NoSentryForIdentificatorException($sentryIdentificator, $e);
		}

		switch ($sentryIdentificatorParseResult->getType()) {
			case 'int':
			case 'string':
			case 'bool':
			case 'float':
			case 'integer':
			case 'boolean':
			case 'mixed':
				return ($sentryIdentificatorParseResult->isMany()) ? new CollectionType() : new SimpleType();
			default:
				if (
					class_exists($sentryIdentificatorParseResult->getType())
					|| interface_exists($sentryIdentificatorParseResult->getType())
				) {
					return ($sentryIdentificatorParseResult->isMany()) ? new CollectionType() : new SimpleType();
				}
				throw new \Consistence\Sentry\Factory\NoSentryForIdentificatorException($sentryIdentificator);
		}
	}

}
