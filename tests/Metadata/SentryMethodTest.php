<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

class SentryMethodTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate()
	{
		$sentryMethod = new SentryMethod(
			new SentryAccess('get'),
			'foo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);

		$this->assertTrue($sentryMethod->getSentryAccess()->equals(new SentryAccess('get')));
		$this->assertSame('foo', $sentryMethod->getMethodName());
		$this->assertSame(Visibility::get(Visibility::VISIBILITY_PUBLIC), $sentryMethod->getMethodVisibility());
	}

}
