<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

use Generator;
use PHPUnit\Framework\Assert;
use stdClass;

class SentryIdentificatorTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function createDataProvider(): Generator
	{
		yield 'id is string' => [
			'id' => 'foo',
		];
		yield 'is is object' => [
			'id' => new stdClass(),
		];
	}

	/**
	 * @dataProvider createDataProvider
	 *
	 * @param mixed $id
	 */
	public function testCreate($id): void
	{
		$sentryIdentificator = new SentryIdentificator($id);
		Assert::assertSame($id, $sentryIdentificator->getId());
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function equalsDataProvider(): Generator
	{
		yield 'same name' => [
			'firstSentryIdentificator' => new SentryIdentificator('foo'),
			'secondSentryIdentificator' => new SentryIdentificator('foo'),
			'expectedEquals' => true,
		];
		yield 'different name' => [
			'firstSentryIdentificator' => new SentryIdentificator('foo'),
			'secondSentryIdentificator' => new SentryIdentificator('bar'),
			'expectedEquals' => false,
		];
	}

	/**
	 * @dataProvider equalsDataProvider
	 *
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $firstSentryIdentificator
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $secondSentryIdentificator
	 * @param bool $expectedEquals
	 */
	public function testEquals(
		SentryIdentificator $firstSentryIdentificator,
		SentryIdentificator $secondSentryIdentificator,
		bool $expectedEquals
	): void
	{
		Assert::assertSame($expectedEquals, $firstSentryIdentificator->equals($secondSentryIdentificator));
	}

}
