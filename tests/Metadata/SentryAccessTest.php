<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

use PHPUnit\Framework\Assert;

class SentryAccessTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate(): void
	{
		$sentryAccess = new SentryAccess('foo');
		Assert::assertSame('foo', $sentryAccess->getName());
	}

	public function testEquals(): void
	{
		$sentryAccess = new SentryAccess('foo');
		Assert::assertTrue($sentryAccess->equals(new SentryAccess('foo')));
	}

	public function testNotEquals(): void
	{
		$sentryAccess = new SentryAccess('foo');
		Assert::assertFalse($sentryAccess->equals(new SentryAccess('bar')));
	}

}
