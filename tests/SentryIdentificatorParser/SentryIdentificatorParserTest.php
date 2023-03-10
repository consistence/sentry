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
			'sentryIdentificator' => new SentryIdentificator('string'),
			'expectedType' => 'string',
			'expectedMany' => false,
			'expectedNullable' => false,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('string[]'),
			'expectedType' => 'string',
			'expectedMany' => true,
			'expectedNullable' => false,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('string|NULL'),
			'expectedType' => 'string',
			'expectedMany' => false,
			'expectedNullable' => true,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('string|null'),
			'expectedType' => 'string',
			'expectedMany' => false,
			'expectedNullable' => true,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('string[]|NULL'),
			'expectedType' => 'string',
			'expectedMany' => true,
			'expectedNullable' => true,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('string[]|null'),
			'expectedType' => 'string',
			'expectedMany' => true,
			'expectedNullable' => true,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('string[][]'),
			'expectedType' => 'string',
			'expectedMany' => true,
			'expectedNullable' => false,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('string[][]|null'),
			'expectedType' => 'string',
			'expectedMany' => true,
			'expectedNullable' => true,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('Foo'),
			'expectedType' => 'Foo',
			'expectedMany' => false,
			'expectedNullable' => false,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('\Foo'),
			'expectedType' => 'Foo',
			'expectedMany' => false,
			'expectedNullable' => false,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('Foo\Bar'),
			'expectedType' => 'Foo\Bar',
			'expectedMany' => false,
			'expectedNullable' => false,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('\Foo\Bar'),
			'expectedType' => 'Foo\Bar',
			'expectedMany' => false,
			'expectedNullable' => false,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('\Foo\Bar[]'),
			'expectedType' => 'Foo\Bar',
			'expectedMany' => true,
			'expectedNullable' => false,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('\Foo\Bar[]|null'),
			'expectedType' => 'Foo\Bar',
			'expectedMany' => true,
			'expectedNullable' => true,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('\Foo\Bar foobar'),
			'expectedType' => 'Foo\Bar',
			'expectedMany' => false,
			'expectedNullable' => false,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('\Foo\Bar nullable'),
			'expectedType' => 'Foo\Bar',
			'expectedMany' => false,
			'expectedNullable' => false,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('\Collection of \Foo\Bar'),
			'expectedType' => 'Collection',
			'expectedMany' => false,
			'expectedNullable' => false,
			'sourceClass' => null,
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('Foo::Bar'),
			'expectedType' => 'Bar',
			'expectedMany' => false,
			'expectedNullable' => false,
			'sourceClass' => 'Foo',
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('\Foo::\Bar'),
			'expectedType' => 'Bar',
			'expectedMany' => false,
			'expectedNullable' => false,
			'sourceClass' => 'Foo',
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('Long\Class\Name\Which\Tests\The\Backtracking\Limit::Bar'),
			'expectedType' => 'Bar',
			'expectedMany' => false,
			'expectedNullable' => false,
			'sourceClass' => 'Long\Class\Name\Which\Tests\The\Backtracking\Limit',
		];
	}

	/**
	 * @return string[][]|\Generator
	 */
	public function doesNotMatchDataProvider(): Generator
	{
		yield [
			'pattern' => '',
		];
		yield [
			'pattern' => 'Long\Class\Name\Which\Tests\The\Backtracking\Limit::',
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
