<?php

declare(strict_types = 1);

namespace Consistence\Sentry\SentryIdentificatorParser;

use Consistence\Sentry\Metadata\SentryIdentificator;
use Generator;
use PHPUnit\Framework\Assert;

class SentryIdentificatorParserTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function matchesDataProvider(): Generator
	{
		yield [
			new SentryIdentificator('string'),
			'string',
			false,
			false,
			null,
		];
		yield [
			new SentryIdentificator('string[]'),
			'string',
			true,
			false,
			null,
		];
		yield [
			new SentryIdentificator('string|NULL'),
			'string',
			false,
			true,
			null,
		];
		yield [
			new SentryIdentificator('string|null'),
			'string',
			false,
			true,
			null,
		];
		yield [
			new SentryIdentificator('string[]|NULL'),
			'string',
			true,
			true,
			null,
		];
		yield [
			new SentryIdentificator('string[]|null'),
			'string',
			true,
			true,
			null,
		];
		yield [
			new SentryIdentificator('string[][]'),
			'string',
			true,
			false,
			null,
		];
		yield [
			new SentryIdentificator('string[][]|null'),
			'string',
			true,
			true,
			null,
		];
		yield [
			new SentryIdentificator('Foo'),
			'Foo',
			false,
			false,
			null,
		];
		yield [
			new SentryIdentificator('\Foo'),
			'Foo',
			false,
			false,
			null,
		];
		yield [
			new SentryIdentificator('Foo\Bar'),
			'Foo\Bar',
			false,
			false,
			null,
		];
		yield [
			new SentryIdentificator('\Foo\Bar'),
			'Foo\Bar',
			false,
			false,
			null,
		];
		yield [
			new SentryIdentificator('\Foo\Bar[]'),
			'Foo\Bar',
			true,
			false,
			null,
		];
		yield [
			new SentryIdentificator('\Foo\Bar[]|null'),
			'Foo\Bar',
			true,
			true,
			null,
		];
		yield [
			new SentryIdentificator('\Foo\Bar foobar'),
			'Foo\Bar',
			false,
			false,
			null,
		];
		yield [
			new SentryIdentificator('\Foo\Bar nullable'),
			'Foo\Bar',
			false,
			false,
			null,
		];
		yield [
			new SentryIdentificator('\Collection of \Foo\Bar'),
			'Collection',
			false,
			false,
			null,
		];
		yield [
			new SentryIdentificator('Foo::Bar'),
			'Bar',
			false,
			false,
			'Foo',
		];
		yield [
			new SentryIdentificator('\Foo::\Bar'),
			'Bar',
			false,
			false,
			'Foo',
		];
		yield [
			new SentryIdentificator('Long\Class\Name\Which\Tests\The\Backtracking\Limit::Bar'),
			'Bar',
			false,
			false,
			'Long\Class\Name\Which\Tests\The\Backtracking\Limit',
		];
	}

	/**
	 * @return string[][]|\Generator
	 */
	public function doesNotMatchDataProvider(): Generator
	{
		yield [
			'',
		];
		yield [
			'Long\Class\Name\Which\Tests\The\Backtracking\Limit::',
		];
	}

	/**
	 * @dataProvider matchesDataProvider
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
	 * @dataProvider doesNotMatchDataProvider
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
