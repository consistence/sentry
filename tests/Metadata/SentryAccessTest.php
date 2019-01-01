<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

class SentryAccessTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate(): void
	{
		$sentryAccess = new SentryAccess('foo');
		$this->assertSame('foo', $sentryAccess->getName());
	}

	public function testEquals(): void
	{
		$sentryAccess = new SentryAccess('foo');
		$this->assertTrue($sentryAccess->equals(new SentryAccess('foo')));
	}

	public function testNotEquals(): void
	{
		$sentryAccess = new SentryAccess('foo');
		$this->assertFalse($sentryAccess->equals(new SentryAccess('bar')));
	}

}
