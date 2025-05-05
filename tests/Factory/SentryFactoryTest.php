<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Factory;

use Consistence\Sentry\Metadata\SentryIdentificator;
use PHPUnit\Framework\Assert;

class SentryFactoryTest extends \PHPUnit\Framework\TestCase
{

	public function testGetSentry(): void
	{
		$factory = $this->createMock(SentryFactory::class);
		$factory
			->expects(self::once())
			->method('getSentry');

		$factory->getSentry(new SentryIdentificator('string'));
	}

	public function testNoSentry(): void
	{
		$sentryIdentificator = new SentryIdentificator('string');
		$factory = $this->createMock(SentryFactory::class);
		$factory
			->expects(self::once())
			->method('getSentry')
			->will(self::throwException(new \Consistence\Sentry\Factory\NoSentryForIdentificatorException($sentryIdentificator)));

		try {
			$factory->getSentry($sentryIdentificator);
			Assert::fail('Exception expected');
		} catch (\Consistence\Sentry\Factory\NoSentryForIdentificatorException $e) {
			Assert::assertSame($sentryIdentificator, $e->getSentryIdentificator());
		}
	}

}
