<?php

namespace Consistence\Sentry\Type;

/**
 * @see AbstractSentryTest for tests of functionality
 */
class TypeHelperTest extends \PHPUnit\Framework\TestCase
{

	public function testStaticConstruct()
	{
		$this->expectException(\Consistence\StaticClassException::class);

		new TypeHelper();
	}

}
