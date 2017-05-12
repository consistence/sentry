<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SentryIdentificatorParser;

use Consistence\RegExp\RegExp;
use Consistence\Sentry\Metadata\SentryIdentificator;

/**
 * Parsing Sentry Identificators for default implementations
 */
class SentryIdentificatorParser extends \Consistence\ObjectPrototype
{

	const MATCH_TYPE = 'type';
	const MATCH_MANY = 'many';
	const MATCH_NULLABLE = 'nullable';
	const MATCH_SOURCE_CLASS = 'sourceClass';

	const SOURCE_CLASS_SEPARATOR = '::';

	public function parse(SentryIdentificator $sentryIdentificator): SentryIdentificatorParseResult
	{
		$pattern
			= '~^'
			. '(\\\?(?P<' . self::MATCH_SOURCE_CLASS . '>(?:\\\?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)++)' . self::SOURCE_CLASS_SEPARATOR . ')?'
			. '\\\?(?P<' . self::MATCH_TYPE . '>(?:\\\?[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)++)'
			. '(?P<' . self::MATCH_MANY . '>\[\])*'
			. '(?:\|(?P<' . self::MATCH_NULLABLE . '>(?:null)|(?:NULL)))?'
			. '(\s|$)~';
		$matches = RegExp::match($sentryIdentificator->getId(), $pattern);
		if (count($matches) === 0) {
			throw new \Consistence\Sentry\SentryIdentificatorParser\PatternDoesNotMatchException($sentryIdentificator);
		}

		return new SentryIdentificatorParseResult(
			$sentryIdentificator,
			$matches[self::MATCH_TYPE],
			isset($matches[self::MATCH_MANY]) && $matches[self::MATCH_MANY] !== '',
			isset($matches[self::MATCH_NULLABLE]) && $matches[self::MATCH_NULLABLE] !== '',
			(isset($matches[self::MATCH_SOURCE_CLASS]) && $matches[self::MATCH_SOURCE_CLASS] !== '') ? $matches[self::MATCH_SOURCE_CLASS] : null
		);
	}

}
