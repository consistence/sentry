<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Factory;

use Consistence\Sentry\Metadata\SentryIdentificator;

class SentryFactoryTest extends \PHPUnit\Framework\TestCase
{

	public function testGetSentry()
	{
		$factory = $this->createMock(SentryFactory::class);
		$factory
			->expects($this->once())
			->method('getSentry');

		$factory->getSentry(new SentryIdentificator('string'));
	}

	public function testNoSentry()
	{
		$sentryIdentificator = new SentryIdentificator('string');
		$factory = $this->createMock(SentryFactory::class);
		$factory
			->expects($this->once())
			->method('getSentry')
			->will($this->throwException(new \Consistence\Sentry\Factory\NoSentryForIdentificatorException($sentryIdentificator)));

		try {
			$factory->getSentry($sentryIdentificator);
			$this->fail();
		} catch (\Consistence\Sentry\Factory\NoSentryForIdentificatorException $e) {
			$this->assertSame($sentryIdentificator, $e->getSentryIdentificator());
		}
	}

}
