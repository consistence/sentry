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
use PHPUnit\Framework\Assert;
use ReflectionClass;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\visitor\vfsStreamStructureVisitor;

class SentryGeneratorTest extends \PHPUnit\Framework\TestCase
{

	public function testGenerateClass(): void
	{
		vfsStream::setup('sentry');

		$classReflection = new ReflectionClass(new FooClass());

		$classFinder = $this->createMock(ClassFinder::class);

		$sentryIdentificator = new SentryIdentificator('string');

		$fooMethod = $this->createMock(SentryMethod::class);

		$fooProperty = $this->createMock(PropertyMetadata::class);
		$fooProperty
			->expects(self::once())
			->method('getSentryIdentificator')
			->will(self::returnValue($sentryIdentificator));
		$fooProperty
			->expects(self::once())
			->method('getSentryMethods')
			->will(self::returnValue([$fooMethod]));

		$classMetadata = $this->createMock(ClassMetadata::class);
		$classMetadata
			->expects(self::once())
			->method('getProperties')
			->will(self::returnValue([$fooProperty]));

		$metadataSource = $this->createMock(MetadataSource::class);
		$metadataSource
			->expects(self::once())
			->method('getMetadataForClass')
			->with($classReflection)
			->will(self::returnValue($classMetadata));

		$sentry = $this->createMock(Sentry::class);
		$sentry
			->expects(self::once())
			->method('generateMethod')
			->with(Assert::identicalTo($fooProperty), Assert::identicalTo($fooMethod))
			->will(self::returnValue('function test() {}'));

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects(self::once())
			->method('getSentry')
			->with(Assert::identicalTo($sentryIdentificator))
			->will(self::returnValue($sentry));

		$generator = new SentryGenerator(
			$classFinder,
			$metadataSource,
			$sentryFactory,
			vfsStream::url('sentry')
		);

		$fileName = $generator->generateClass($classReflection);
		Assert::assertFileExists($fileName);
	}

	public function testGenerateClassSkipMethodNameCaseInsensitive(): void
	{
		vfsStream::setup('sentry');

		$classReflection = new ReflectionClass(new FooClass());

		$classFinder = $this->createMock(ClassFinder::class);

		$sentryIdentificator = new SentryIdentificator('string');

		$fooMethod = $this->createMock(SentryMethod::class);
		$fooMethod
			->expects(self::once())
			->method('getMethodName')
			->will(self::returnValue('GETskipPROPERTY'));

		$fooProperty = $this->createMock(PropertyMetadata::class);
		$fooProperty
			->expects(self::once())
			->method('getSentryIdentificator')
			->will(self::returnValue($sentryIdentificator));
		$fooProperty
			->expects(self::once())
			->method('getSentryMethods')
			->will(self::returnValue([$fooMethod]));

		$classMetadata = $this->createMock(ClassMetadata::class);
		$classMetadata
			->expects(self::once())
			->method('getProperties')
			->will(self::returnValue([$fooProperty]));

		$metadataSource = $this->createMock(MetadataSource::class);
		$metadataSource
			->expects(self::once())
			->method('getMetadataForClass')
			->with($classReflection)
			->will(self::returnValue($classMetadata));

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects(self::once())
			->method('getSentry')
			->with(Assert::identicalTo($sentryIdentificator));

		$generator = new SentryGenerator(
			$classFinder,
			$metadataSource,
			$sentryFactory,
			vfsStream::url('sentry')
		);

		try {
			$generator->generateClass($classReflection);
			Assert::fail('Exception expected');
		} catch (\Consistence\Sentry\Generated\NoMethodsToBeGeneratedException $e) {
			Assert::assertSame($classMetadata, $e->getClassMetadata());
		}
	}

	public function testGenerateClassNoSentryMethods(): void
	{
		vfsStream::setup('sentry');

		$classReflection = new ReflectionClass(new FooClass());

		$classFinder = $this->createMock(ClassFinder::class);

		$classMetadata = $this->createMock(ClassMetadata::class);
		$classMetadata
			->expects(self::once())
			->method('getProperties')
			->will(self::returnValue([]));

		$metadataSource = $this->createMock(MetadataSource::class);
		$metadataSource
			->expects(self::once())
			->method('getMetadataForClass')
			->with($classReflection)
			->will(self::returnValue($classMetadata));

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects(self::never())
			->method('getSentry');

		$generator = new SentryGenerator(
			$classFinder,
			$metadataSource,
			$sentryFactory,
			vfsStream::url('sentry')
		);

		try {
			$generator->generateClass($classReflection);
			Assert::fail('Exception expected');
		} catch (\Consistence\Sentry\Generated\NoMethodsToBeGeneratedException $e) {
			Assert::assertSame($classMetadata, $e->getClassMetadata());
		}
	}

	public function testGenerateAll(): void
	{
		vfsStream::setup('sentry');

		$classReflection = new ReflectionClass(new FooClass());

		$classFinder = $this->createMock(ClassFinder::class);
		$classFinder
			->expects(self::once())
			->method('findByInterface')
			->will(self::returnValue([FooClass::class]));

		$sentryIdentificator = new SentryIdentificator('string');

		$fooMethod = $this->createMock(SentryMethod::class);

		$fooProperty = $this->createMock(PropertyMetadata::class);
		$fooProperty
			->expects(self::once())
			->method('getSentryIdentificator')
			->will(self::returnValue($sentryIdentificator));
		$fooProperty
			->expects(self::once())
			->method('getSentryMethods')
			->will(self::returnValue([$fooMethod]));

		$classMetadata = $this->createMock(ClassMetadata::class);
		$classMetadata
			->expects(self::once())
			->method('getProperties')
			->will(self::returnValue([$fooProperty]));

		$metadataSource = $this->createMock(MetadataSource::class);
		$metadataSource
			->expects(self::once())
			->method('getMetadataForClass')
			->with($classReflection)
			->will(self::returnValue($classMetadata));

		$sentry = $this->createMock(Sentry::class);
		$sentry
			->expects(self::once())
			->method('generateMethod')
			->with(Assert::identicalTo($fooProperty), Assert::identicalTo($fooMethod))
			->will(self::returnValue('function test() {}'));

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects(self::once())
			->method('getSentry')
			->with(Assert::identicalTo($sentryIdentificator))
			->will(self::returnValue($sentry));

		$generator = new SentryGenerator(
			$classFinder,
			$metadataSource,
			$sentryFactory,
			vfsStream::url('sentry')
		);

		$generated = $generator->generateAll();
		Assert::assertCount(1, $generated);

		$structure = vfsStream::inspect(new vfsStreamStructureVisitor())->getStructure();
		Assert::assertCount(1, $structure['sentry']);
		Assert::assertTrue(isset($structure['sentry']['Consistence_Sentry_Generated_FooClass.php']));
	}

	public function testGenerateAllNoMethods(): void
	{
		vfsStream::setup('sentry');

		$classReflection = new ReflectionClass(new FooClass());

		$classFinder = $this->createMock(ClassFinder::class);
		$classFinder
			->expects(self::once())
			->method('findByInterface')
			->will(self::returnValue([FooClass::class]));

		$classMetadata = $this->createMock(ClassMetadata::class);
		$classMetadata
			->expects(self::once())
			->method('getProperties')
			->will(self::returnValue([]));

		$metadataSource = $this->createMock(MetadataSource::class);
		$metadataSource
			->expects(self::once())
			->method('getMetadataForClass')
			->with($classReflection)
			->will(self::returnValue($classMetadata));

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects(self::never())
			->method('getSentry');

		$generator = new SentryGenerator(
			$classFinder,
			$metadataSource,
			$sentryFactory,
			vfsStream::url('sentry')
		);

		$generated = $generator->generateAll();
		Assert::assertCount(0, $generated);

		$structure = vfsStream::inspect(new vfsStreamStructureVisitor())->getStructure();
		Assert::assertCount(0, $structure['sentry']);
	}

}
