<?php

declare(strict_types = 1);

namespace Consistence\Sentry\MetadataSource;

use PHPUnit\Framework\Assert;
use ReflectionClass;

class MetadataSourceTest extends \PHPUnit\Framework\TestCase
{

	public function testGetSentry(): void
	{
		$factory = $this->createMock(MetadataSource::class);
		$factory
			->expects(self::once())
			->method('getMetadataForClass');

		$factory->getMetadataForClass(new ReflectionClass(FooClass::class));
	}

	public function testCouldNotBeCreated(): void
	{
		$classReflection = new ReflectionClass(FooClass::class);
		$factory = $this->createMock(MetadataSource::class);
		$factory
			->expects(self::once())
			->method('getMetadataForClass')
			->will(self::throwException(new \Consistence\Sentry\MetadataSource\ClassMetadataCouldNotBeCreatedException($classReflection, 'test')));

		try {
			$factory->getMetadataForClass($classReflection);
			Assert::fail();
		} catch (\Consistence\Sentry\MetadataSource\ClassMetadataCouldNotBeCreatedException $e) {
			Assert::assertSame($classReflection, $e->getClassReflection());
			Assert::assertStringContainsString(FooClass::class, $e->getMessage());
			Assert::assertStringContainsString('test', $e->getMessage());
		}
	}

}
