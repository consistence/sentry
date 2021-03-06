<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Factory\Simple;

use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\SentryIdentificatorParser\SentryIdentificatorParser;
use Consistence\Sentry\Type\CollectionType;
use Consistence\Sentry\Type\Sentry;
use Consistence\Sentry\Type\SimpleType;

class SimpleSentryFactoryTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]
	 */
	public function sentryIdentificatorToSentryProvider(): array
	{
		return [
			[new SentryIdentificator('string'), new SimpleType()],
			[new SentryIdentificator('int'), new SimpleType()],
			[new SentryIdentificator('bool'), new SimpleType()],
			[new SentryIdentificator('float'), new SimpleType()],
			[new SentryIdentificator('integer'), new SimpleType()],
			[new SentryIdentificator('boolean'), new SimpleType()],
			[new SentryIdentificator('mixed'), new SimpleType()],
			[new SentryIdentificator('DateTimeImmutable'), new SimpleType()],
			[new SentryIdentificator('string[]'), new CollectionType()],
			[new SentryIdentificator('int[]'), new CollectionType()],
			[new SentryIdentificator('bool[]'), new CollectionType()],
			[new SentryIdentificator('float[]'), new CollectionType()],
			[new SentryIdentificator('integer[]'), new CollectionType()],
			[new SentryIdentificator('boolean[]'), new CollectionType()],
			[new SentryIdentificator('mixed[]'), new CollectionType()],
			[new SentryIdentificator('DateTimeImmutable[]'), new CollectionType()],
		];
	}

	/**
	 * @dataProvider sentryIdentificatorToSentryProvider
	 *
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $sentryIdentificator
	 * @param \Consistence\Sentry\Type\Sentry $sentry
	 */
	public function testGetSentry(SentryIdentificator $sentryIdentificator, Sentry $sentry): void
	{
		$factory = new SimpleSentryFactory(new SentryIdentificatorParser());
		$this->assertTrue($factory->getSentry($sentryIdentificator) instanceof $sentry);
	}

	public function testSameSentryInstance(): void
	{
		$factory = new SimpleSentryFactory(new SentryIdentificatorParser());
		$sentry = $factory->getSentry(new SentryIdentificator('string'));
		$this->assertSame($sentry, $factory->getSentry(new SentryIdentificator('string')));
	}

	public function testNoSentry(): void
	{
		$factory = new SimpleSentryFactory(new SentryIdentificatorParser());

		$this->expectException(\Consistence\Sentry\Factory\NoSentryForIdentificatorException::class);

		$factory->getSentry(new SentryIdentificator(''));
	}

	public function testNonexistingObject(): void
	{
		$factory = new SimpleSentryFactory(new SentryIdentificatorParser());

		$this->expectException(\Consistence\Sentry\Factory\NoSentryForIdentificatorException::class);
		$this->expectExceptionMessage('Foo\Bar');

		$factory->getSentry(new SentryIdentificator('Foo\Bar'));
	}

}
