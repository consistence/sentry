<?php

declare(strict_types = 1);

namespace Consistence\Sentry\MetadataSource;

use ReflectionClass;

class MetadataSourceTest extends \PHPUnit\Framework\TestCase
{

	public function testGetSentry(): void
	{
		$factory = $this->createMock(MetadataSource::class);
		$factory
			->expects($this->once())
			->method('getMetadataForClass');

		$factory->getMetadataForClass(new ReflectionClass(FooClass::class));
	}

	public function testCouldNotBeCreated(): void
	{
		$classReflection = new ReflectionClass(FooClass::class);
		$factory = $this->createMock(MetadataSource::class);
		$factory
			->expects($this->once())
			->method('getMetadataForClass')
			->will($this->throwException(new \Consistence\Sentry\MetadataSource\ClassMetadataCouldNotBeCreatedException($classReflection, 'test')));

		try {
			$factory->getMetadataForClass($classReflection);
			$this->fail();
		} catch (\Consistence\Sentry\MetadataSource\ClassMetadataCouldNotBeCreatedException $e) {
			$this->assertSame($classReflection, $e->getClassReflection());
			$this->assertContains(FooClass::class, $e->getMessage());
			$this->assertContains('test', $e->getMessage());
		}
	}

}
