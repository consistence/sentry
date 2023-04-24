<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

use Generator;
use PHPUnit\Framework\Assert;

class SentryAccessTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate(): void
	{
		$sentryAccess = new SentryAccess('foo');
		Assert::assertSame('foo', $sentryAccess->getName());
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function equalsDataProvider(): Generator
	{
		yield 'same name' => [
			'firstSentryAccess' => new SentryAccess('foo'),
			'secondSentryAccess' => new SentryAccess('foo'),
			'expectedEquals' => true,
		];
		yield 'different name' => [
			'firstSentryAccess' => new SentryAccess('foo'),
			'secondSentryAccess' => new SentryAccess('bar'),
			'expectedEquals' => false,
		];
	}

	/**
	 * @dataProvider equalsDataProvider
	 *
	 * @param \Consistence\Sentry\Metadata\SentryAccess $firstSentryAccess
	 * @param \Consistence\Sentry\Metadata\SentryAccess $secondSentryAccess
	 * @param bool $expectedEquals
	 */
	public function testEquals(
		SentryAccess $firstSentryAccess,
		SentryAccess $secondSentryAccess,
		bool $expectedEquals
	): void
	{
		Assert::assertSame($expectedEquals, $firstSentryAccess->equals($secondSentryAccess));
	}

}
