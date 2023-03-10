<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Factory\Simple;

use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\SentryIdentificatorParser\SentryIdentificatorParser;
use Consistence\Sentry\Type\CollectionType;
use Consistence\Sentry\Type\Sentry;
use Consistence\Sentry\Type\SimpleType;
use Generator;
use PHPUnit\Framework\Assert;

class SimpleSentryFactoryTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function sentryIdentificatorToSentryDataProvider(): Generator
	{
		yield [
			'sentryIdentificator' => new SentryIdentificator('string'),
			'sentry' => new SimpleType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('int'),
			'sentry' => new SimpleType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('bool'),
			'sentry' => new SimpleType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('float'),
			'sentry' => new SimpleType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('integer'),
			'sentry' => new SimpleType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('boolean'),
			'sentry' => new SimpleType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('mixed'),
			'sentry' => new SimpleType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('DateTimeImmutable'),
			'sentry' => new SimpleType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('string[]'),
			'sentry' => new CollectionType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('int[]'),
			'sentry' => new CollectionType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('bool[]'),
			'sentry' => new CollectionType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('float[]'),
			'sentry' => new CollectionType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('integer[]'),
			'sentry' => new CollectionType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('boolean[]'),
			'sentry' => new CollectionType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('mixed[]'),
			'sentry' => new CollectionType(),
		];
		yield [
			'sentryIdentificator' => new SentryIdentificator('DateTimeImmutable[]'),
			'sentry' => new CollectionType(),
		];
	}

	/**
	 * @dataProvider sentryIdentificatorToSentryDataProvider
	 *
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $sentryIdentificator
	 * @param \Consistence\Sentry\Type\Sentry $sentry
	 */
	public function testGetSentry(SentryIdentificator $sentryIdentificator, Sentry $sentry): void
	{
		$factory = new SimpleSentryFactory(new SentryIdentificatorParser());
		Assert::assertTrue($factory->getSentry($sentryIdentificator) instanceof $sentry);
	}

	public function testSameSentryInstance(): void
	{
		$factory = new SimpleSentryFactory(new SentryIdentificatorParser());
		$sentry = $factory->getSentry(new SentryIdentificator('string'));
		Assert::assertSame($sentry, $factory->getSentry(new SentryIdentificator('string')));
	}

	public function testNoSentry(): void
	{
		$factory = new SimpleSentryFactory(new SentryIdentificatorParser());
		$sentryIdentificator = new SentryIdentificator('');

		try {
			$factory->getSentry($sentryIdentificator);
			Assert::fail('Exception expected');
		} catch (\Consistence\Sentry\Factory\NoSentryForIdentificatorException $e) {
			Assert::assertSame($sentryIdentificator, $e->getSentryIdentificator());
		}
	}

	public function testNonexistingObject(): void
	{
		$factory = new SimpleSentryFactory(new SentryIdentificatorParser());
		$sentryIdentificator = new SentryIdentificator('Foo\Bar');

		try {
			$factory->getSentry($sentryIdentificator);
			Assert::fail('Exception expected');
		} catch (\Consistence\Sentry\Factory\NoSentryForIdentificatorException $e) {
			Assert::assertSame($sentryIdentificator, $e->getSentryIdentificator());
		}
	}

}
