<?php

namespace Consistence\Sentry\SentryIdentificatorParser;

use Consistence\Sentry\Metadata\SentryIdentificator;

class SentryIdentificatorParserTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]
	 */
	public function matchesProvider()
	{
		return [
			[new SentryIdentificator('string'), 'string', false, false],
			[new SentryIdentificator('string[]'), 'string', true, false],
			[new SentryIdentificator('string|NULL'), 'string', false, true],
			[new SentryIdentificator('string|null'), 'string', false, true],
			[new SentryIdentificator('string[]|NULL'), 'string', true, true],
			[new SentryIdentificator('string[]|null'), 'string', true, true],
			[new SentryIdentificator('Foo'), 'Foo', false, false],
			[new SentryIdentificator('\Foo'), 'Foo', false, false],
			[new SentryIdentificator('Foo\Bar'), 'Foo\Bar', false, false],
			[new SentryIdentificator('\Foo\Bar'), 'Foo\Bar', false, false],
			[new SentryIdentificator('\Foo\Bar[]'), 'Foo\Bar', true, false],
			[new SentryIdentificator('\Foo\Bar[]|null'), 'Foo\Bar', true, true],
			[new SentryIdentificator('\Foo\Bar foobar'), 'Foo\Bar', false, false],
			[new SentryIdentificator('\Foo\Bar nullable'), 'Foo\Bar', false, false],
			[new SentryIdentificator('\Collection of \Foo\Bar'), 'Collection', false, false],
		];
	}

	/**
	 * @dataProvider matchesProvider
	 *
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $sentryIdentificator
	 * @param string $expectedType
	 * @param boolean $expectedMany
	 * @param boolean $expectedNullable
	 */
	public function testMatch(SentryIdentificator $sentryIdentificator, $expectedType, $expectedMany, $expectedNullable)
	{
		$parser = new SentryIdentificatorParser();
		$result = $parser->parse($sentryIdentificator);
		$this->assertInstanceOf(SentryIdentificatorParseResult::class, $result);
		$this->assertSame($sentryIdentificator, $result->getSentryIdentificator());
		$this->assertSame($expectedType, $result->getType());
		$this->assertSame($expectedMany, $result->isMany());
		$this->assertSame($expectedNullable, $result->isNullable());
	}

	public function testDoesNotMatch()
	{
		$parser = new SentryIdentificatorParser();
		$sentryIdentificator = new SentryIdentificator('');
		try {
			$parser->parse($sentryIdentificator);
			$this->fail();
		} catch (\Consistence\Sentry\SentryIdentificatorParser\PatternDoesNotMatchException $e) {
			$this->assertSame($sentryIdentificator, $e->getSentryIdentificator());
		}
	}

}
