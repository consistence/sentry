<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Generated;

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
			->expects($this->once())
			->method('generateAll')
			->will($this->returnValue($classMap));

		$autoloader = new SentryAutoloader($generator, $targetPath);

		$this->assertFalse($autoloader->isClassMapReady());

		$autoloader->rebuild();

		$this->assertTrue($autoloader->isClassMapReady());

		$this->assertEquals($classMap, require $targetPath);
	}

}
