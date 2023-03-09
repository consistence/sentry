<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SentryIdentificatorParser;

use Consistence\Sentry\Metadata\SentryIdentificator;
use PHPUnit\Framework\Assert;

class SentryIdentificatorParserTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]
	 */
	public function matchesProvider(): array
	{
		return [
			[new SentryIdentificator('string'), 'string', false, false, null],
			[new SentryIdentificator('string[]'), 'string', true, false, null],
			[new SentryIdentificator('string|NULL'), 'string', false, true, null],
			[new SentryIdentificator('string|null'), 'string', false, true, null],
			[new SentryIdentificator('string[]|NULL'), 'string', true, true, null],
			[new SentryIdentificator('string[]|null'), 'string', true, true, null],
			[new SentryIdentificator('string[][]'), 'string', true, false, null],
			[new SentryIdentificator('string[][]|null'), 'string', true, true, null],
			[new SentryIdentificator('Foo'), 'Foo', false, false, null],
			[new SentryIdentificator('\Foo'), 'Foo', false, false, null],
			[new SentryIdentificator('Foo\Bar'), 'Foo\Bar', false, false, null],
			[new SentryIdentificator('\Foo\Bar'), 'Foo\Bar', false, false, null],
			[new SentryIdentificator('\Foo\Bar[]'), 'Foo\Bar', true, false, null],
			[new SentryIdentificator('\Foo\Bar[]|null'), 'Foo\Bar', true, true, null],
			[new SentryIdentificator('\Foo\Bar foobar'), 'Foo\Bar', false, false, null],
			[new SentryIdentificator('\Foo\Bar nullable'), 'Foo\Bar', false, false, null],
			[new SentryIdentificator('\Collection of \Foo\Bar'), 'Collection', false, false, null],
			[new SentryIdentificator('Foo::Bar'), 'Bar', false, false, 'Foo'],
			[new SentryIdentificator('\Foo::\Bar'), 'Bar', false, false, 'Foo'],
			[new SentryIdentificator('Long\Class\Name\Which\Tests\The\Backtracking\Limit::Bar'), 'Bar', false, false, 'Long\Class\Name\Which\Tests\The\Backtracking\Limit'],
		];
	}

	/**
	 * @return string[][]
	 */
	public function doesNotMatchProvider(): array
	{
		return [
			[''],
			['Long\Class\Name\Which\Tests\The\Backtracking\Limit::'],
		];
	}

	/**
	 * @dataProvider matchesProvider
	 *
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $sentryIdentificator
	 * @param string $expectedType
	 * @param bool $expectedMany
	 * @param bool $expectedNullable
	 * @param string|null $sourceClass
	 */
	public function testMatch(
		SentryIdentificator $sentryIdentificator,
		string $expectedType,
		bool $expectedMany,
		bool $expectedNullable,
		?string $sourceClass
	): void
	{
		$parser = new SentryIdentificatorParser();
		$result = $parser->parse($sentryIdentificator);
		Assert::assertInstanceOf(SentryIdentificatorParseResult::class, $result);
		Assert::assertSame($sentryIdentificator, $result->getSentryIdentificator());
		Assert::assertSame($expectedType, $result->getType());
		Assert::assertSame($expectedMany, $result->isMany());
		Assert::assertSame($expectedNullable, $result->isNullable());
		Assert::assertSame($sourceClass, $result->getSourceClass());
	}

	/**
	 * @dataProvider doesNotMatchProvider
	 *
	 * @param string $pattern
	 */
	public function testDoesNotMatch(string $pattern): void
	{
		$parser = new SentryIdentificatorParser();
		$sentryIdentificator = new SentryIdentificator($pattern);
		try {
			$parser->parse($sentryIdentificator);
			Assert::fail('Exception expected');
		} catch (\Consistence\Sentry\SentryIdentificatorParser\PatternDoesNotMatchException $e) {
			Assert::assertSame($sentryIdentificator, $e->getSentryIdentificator());
		}
	}

}
