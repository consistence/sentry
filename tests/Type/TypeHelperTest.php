<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

/**
 * @see AbstractSentryTest for tests of functionality
 */
class TypeHelperTest extends \PHPUnit\Framework\TestCase
{

	public function testStaticConstruct(): void
	{
		$this->expectException(\Consistence\StaticClassException::class);

		new TypeHelper();
	}

}
