<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

use stdClass;

class SentryIdentificatorTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate(): void
	{
		$sentryIdentificator = new SentryIdentificator('foo');
		$this->assertSame('foo', $sentryIdentificator->getId());
	}

	public function testCreateObjectId(): void
	{
		$object = new stdClass();
		$sentryIdentificator = new SentryIdentificator($object);
		$this->assertSame($object, $sentryIdentificator->getId());
	}

	public function testEquals(): void
	{
		$sentryIdentificator = new SentryIdentificator('foo');
		$this->assertTrue($sentryIdentificator->equals(new SentryIdentificator('foo')));
	}

	public function testNotEquals(): void
	{
		$sentryIdentificator = new SentryIdentificator('foo');
		$this->assertFalse($sentryIdentificator->equals(new SentryIdentificator('bar')));
	}

}
