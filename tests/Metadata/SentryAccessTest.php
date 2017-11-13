<?php

namespace Consistence\Sentry\Metadata;

class SentryAccessTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate()
	{
		$sentryAccess = new SentryAccess('foo');
		$this->assertSame('foo', $sentryAccess->getName());
	}

	public function testEquals()
	{
		$sentryAccess = new SentryAccess('foo');
		$this->assertTrue($sentryAccess->equals(new SentryAccess('foo')));
	}

	public function testNotEquals()
	{
		$sentryAccess = new SentryAccess('foo');
		$this->assertFalse($sentryAccess->equals(new SentryAccess('bar')));
	}

}
