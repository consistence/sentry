<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

use PHPUnit\Framework\Assert;

class SentryMethodTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate(): void
	{
		$sentryMethod = new SentryMethod(
			new SentryAccess('get'),
			'foo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);

		Assert::assertTrue($sentryMethod->getSentryAccess()->equals(new SentryAccess('get')));
		Assert::assertSame('foo', $sentryMethod->getMethodName());
		Assert::assertSame(Visibility::get(Visibility::VISIBILITY_PUBLIC), $sentryMethod->getMethodVisibility());
	}

}
