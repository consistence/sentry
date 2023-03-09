<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Generated;

use PHPUnit\Framework\Assert;
use org\bovigo\vfs\vfsStream;

class SentryAutoloaderTest extends \PHPUnit\Framework\TestCase
{

	public function testRebuild(): void
	{
		vfsStream::setup('sentry');

		$targetPath = vfsStream::url('sentry') . '/sentryClassMap.php';

		$classMap = [
			FooClass::class => '/test/path',
		];

		$generator = $this->createMock(SentryGenerator::class);
		$generator
			->expects(self::once())
			->method('generateAll')
			->will(self::returnValue($classMap));

		$autoloader = new SentryAutoloader($generator, $targetPath);

		Assert::assertFalse($autoloader->isClassMapReady());

		$autoloader->rebuild();

		Assert::assertTrue($autoloader->isClassMapReady());

		Assert::assertEquals($classMap, require $targetPath);
	}

}
