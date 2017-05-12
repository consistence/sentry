<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Generated;

use Consistence\ClassFinder\ClassFinder;
use Consistence\Sentry\Factory\SentryFactory;
use Consistence\Sentry\Metadata\ClassMetadata;
use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\MetadataSource\MetadataSource;
use Consistence\Sentry\Type\Sentry;
use ReflectionClass;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;

class SentryGeneratorTest extends \PHPUnit\Framework\TestCase
{

	public function testGenerateClass()
	{
		vfsStream::setup('sentry');

		$classReflection = new ReflectionClass(new FooClass());

		$classFinder = $this->createMock(ClassFinder::class);

		$sentryIdentificator = new SentryIdentificator('string');

		$fooMethod = $this->createMock(SentryMethod::class);

		$fooProperty = $this->createMock(PropertyMetadata::class);
		$fooProperty
			->expects($this->once())
			->method('getSentryIdentificator')
			->will($this->returnValue($sentryIdentificator));
		$fooProperty
			->expects($this->once())
			->method('getSentryMethods')
			->will($this->returnValue([$fooMethod]));

		$classMetadata = $this->createMock(ClassMetadata::class);
		$classMetadata
			->expects($this->once())
			->method('getProperties')
			->will($this->returnValue([$fooProperty]));

		$metadataSource = $this->createMock(MetadataSource::class);
		$metadataSource
			->expects($this->once())
			->method('getMetadataForClass')
			->with($classReflection)
			->will($this->returnValue($classMetadata));

		$sentry = $this->createMock(Sentry::class);
		$sentry
			->expects($this->once())
			->method('generateMethod')
			->with($this->identicalTo($fooProperty), $this->identicalTo($fooMethod))
			->will($this->returnValue('function test() {}'));

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects($this->once())
			->method('getSentry')
			->with($this->identicalTo($sentryIdentificator))
			->will($this->returnValue($sentry));

		$generator = new SentryGenerator(
			$classFinder,
			$metadataSource,
			$sentryFactory,
			vfsStream::url('sentry')
		);

		$fileName = $generator->generateClass($classReflection);
		$this->assertFileExists($fileName);
	}

	public function testGenerateClassSkipMethodNameCaseInsensitive()
	{
		vfsStream::setup('sentry');

		$classReflection = new ReflectionClass(new FooClass());

		$classFinder = $this->createMock(ClassFinder::class);

		$sentryIdentificator = new SentryIdentificator('string');

		$fooMethod = $this->createMock(SentryMethod::class);
		$fooMethod
			->expects($this->once())
			->method('getMethodName')
			->will($this->returnValue('GETskipPROPERTY'));

		$fooProperty = $this->createMock(PropertyMetadata::class);
		$fooProperty
			->expects($this->once())
			->method('getSentryIdentificator')
			->will($this->returnValue($sentryIdentificator));
		$fooProperty
			->expects($this->once())
			->method('getSentryMethods')
			->will($this->returnValue([$fooMethod]));

		$classMetadata = $this->createMock(ClassMetadata::class);
		$classMetadata
			->expects($this->once())
			->method('getProperties')
			->will($this->returnValue([$fooProperty]));

		$metadataSource = $this->createMock(MetadataSource::class);
		$metadataSource
			->expects($this->once())
			->method('getMetadataForClass')
			->with($classReflection)
			->will($this->returnValue($classMetadata));

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects($this->once())
			->method('getSentry')
			->with($this->identicalTo($sentryIdentificator));

		$generator = new SentryGenerator(
			$classFinder,
			$metadataSource,
			$sentryFactory,
			vfsStream::url('sentry')
		);

		try {
			$generator->generateClass($classReflection);
			$this->fail();
		} catch (\Consistence\Sentry\Generated\NoMethodsToBeGeneratedException $e) {
			$this->assertSame($classMetadata, $e->getClassMetadata());
		}
	}

	public function testGenerateClassNoSentryMethods()
	{
		vfsStream::setup('sentry');

		$classReflection = new ReflectionClass(new FooClass());

		$classFinder = $this->createMock(ClassFinder::class);

		$classMetadata = $this->createMock(ClassMetadata::class);
		$classMetadata
			->expects($this->once())
			->method('getProperties')
			->will($this->returnValue([]));

		$metadataSource = $this->createMock(MetadataSource::class);
		$metadataSource
			->expects($this->once())
			->method('getMetadataForClass')
			->with($classReflection)
			->will($this->returnValue($classMetadata));

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects($this->never())
			->method('getSentry');

		$generator = new SentryGenerator(
			$classFinder,
			$metadataSource,
			$sentryFactory,
			vfsStream::url('sentry')
		);

		try {
			$generator->generateClass($classReflection);
			$this->fail();
		} catch (\Consistence\Sentry\Generated\NoMethodsToBeGeneratedException $e) {
			$this->assertSame($classMetadata, $e->getClassMetadata());
		}
	}

	public function testGenerateAll()
	{
		vfsStream::setup('sentry');

		$classReflection = new ReflectionClass(new FooClass());

		$classFinder = $this->createMock(ClassFinder::class);
		$classFinder
			->expects($this->once())
			->method('findByInterface')
			->will($this->returnValue([FooClass::class]));

		$sentryIdentificator = new SentryIdentificator('string');

		$fooMethod = $this->createMock(SentryMethod::class);

		$fooProperty = $this->createMock(PropertyMetadata::class);
		$fooProperty
			->expects($this->once())
			->method('getSentryIdentificator')
			->will($this->returnValue($sentryIdentificator));
		$fooProperty
			->expects($this->once())
			->method('getSentryMethods')
			->will($this->returnValue([$fooMethod]));

		$classMetadata = $this->createMock(ClassMetadata::class);
		$classMetadata
			->expects($this->once())
			->method('getProperties')
			->will($this->returnValue([$fooProperty]));

		$metadataSource = $this->createMock(MetadataSource::class);
		$metadataSource
			->expects($this->once())
			->method('getMetadataForClass')
			->with($classReflection)
			->will($this->returnValue($classMetadata));

		$sentry = $this->createMock(Sentry::class);
		$sentry
			->expects($this->once())
			->method('generateMethod')
			->with($this->identicalTo($fooProperty), $this->identicalTo($fooMethod))
			->will($this->returnValue('function test() {}'));

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects($this->once())
			->method('getSentry')
			->with($this->identicalTo($sentryIdentificator))
			->will($this->returnValue($sentry));

		$generator = new SentryGenerator(
			$classFinder,
			$metadataSource,
			$sentryFactory,
			vfsStream::url('sentry')
		);

		$generated = $generator->generateAll();
		$this->assertCount(1, $generated);

		$structure = vfsStream::inspect(new vfsStreamStructureVisitor())->getStructure();
		$this->assertCount(1, $structure['sentry']);
		$this->assertTrue(isset($structure['sentry']['Consistence_Sentry_Generated_FooClass.php']));
	}

	public function testGenerateAllNoMethods()
	{
		vfsStream::setup('sentry');

		$classReflection = new ReflectionClass(new FooClass());

		$classFinder = $this->createMock(ClassFinder::class);
		$classFinder
			->expects($this->once())
			->method('findByInterface')
			->will($this->returnValue([FooClass::class]));

		$classMetadata = $this->createMock(ClassMetadata::class);
		$classMetadata
			->expects($this->once())
			->method('getProperties')
			->will($this->returnValue([]));

		$metadataSource = $this->createMock(MetadataSource::class);
		$metadataSource
			->expects($this->once())
			->method('getMetadataForClass')
			->with($classReflection)
			->will($this->returnValue($classMetadata));

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects($this->never())
			->method('getSentry');

		$generator = new SentryGenerator(
			$classFinder,
			$metadataSource,
			$sentryFactory,
			vfsStream::url('sentry')
		);

		$generated = $generator->generateAll();
		$this->assertCount(0, $generated);

		$structure = vfsStream::inspect(new vfsStreamStructureVisitor())->getStructure();
		$this->assertCount(0, $structure['sentry']);
	}

}
