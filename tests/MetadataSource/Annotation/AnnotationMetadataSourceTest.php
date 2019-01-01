<?php

declare(strict_types = 1);

namespace Consistence\Sentry\MetadataSource\Annotation;

use Consistence\Annotation\Annotation;
use Consistence\Annotation\AnnotationField;
use Consistence\Annotation\AnnotationProvider;
use Consistence\Sentry\Factory\SentryFactory;
use Consistence\Sentry\Metadata\SentryAccess;
use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\Metadata\Visibility;
use Consistence\Sentry\MetadataSource\FooClass;
use Consistence\Sentry\SentryIdentificatorParser\SentryIdentificatorParser;
use Consistence\Sentry\Type\SimpleType;
use ReflectionClass;
use ReflectionProperty;

class AnnotationMetadataSourceTest extends \PHPUnit\Framework\TestCase
{

	public function testGet(): void
	{
		$type = 'string';
		$className = FooClass::class;
		$propertyName = 'fooProperty';
		$classReflection = new ReflectionClass($className);
		$sentryIdentificator = new SentryIdentificator($className . '::' . $type);

		$sentryIdentificatorAnnotation = Annotation::createAnnotationWithValue(
			AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION,
			$type
		);

		$getAnnotationsCallback = function (ReflectionProperty $property, string $annotationName): array {
			switch ($annotationName) {
				case 'get':
					return [Annotation::createAnnotationWithFields('get', [])];
				case 'set':
					return [];
			}
		};

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects($this->once())
			->method('getSentry')
			->with($sentryIdentificator)
			->will($this->returnValue(new SimpleType()));

		$annotationProvider = $this->createMock(AnnotationProvider::class);
		$annotationProvider
			->expects($this->once())
			->method('getPropertyAnnotation')
			->with($classReflection->getProperty($propertyName), $this->isType('string'))
			->will($this->returnValue($sentryIdentificatorAnnotation));
		$annotationProvider
			->expects($this->exactly(2))
			->method('getPropertyAnnotations')
			->will($this->returnCallback($getAnnotationsCallback));

		$metadataSource = new AnnotationMetadataSource(
			$sentryFactory,
			new SentryIdentificatorParser(),
			$annotationProvider
		);
		$classMetadata = $metadataSource->getMetadataForClass($classReflection);

		$this->assertSame($className, $classMetadata->getName());
		$properties = $classMetadata->getProperties();
		$this->assertCount(1, $properties);
		$fooPropety = $properties[0];
		$this->assertSame($propertyName, $fooPropety->getName());
		$this->assertSame($className, $fooPropety->getClassName());
		$this->assertSame($type, $fooPropety->getType());
		$this->assertTrue($sentryIdentificator->equals($fooPropety->getSentryIdentificator()));
		$this->assertFalse($fooPropety->isNullable());
		$sentryMethods = $fooPropety->getSentryMethods();
		$this->assertCount(1, $sentryMethods);
		$getMethod = $sentryMethods[0];
		$this->assertSame('getFooProperty', $getMethod->getMethodName());
		$this->assertSame(Visibility::get(Visibility::VISIBILITY_PUBLIC), $getMethod->getMethodVisibility());
		$this->assertTrue($getMethod->getSentryAccess()->equals(new SentryAccess('get')));
		$this->assertNull($fooPropety->getBidirectionalAssociation());
	}

	public function testCustomMethodName(): void
	{
		$type = 'string';
		$className = FooClass::class;
		$propertyName = 'fooProperty';
		$classReflection = new ReflectionClass($className);
		$sentryIdentificator = new SentryIdentificator($className . '::' . $type);

		$sentryIdentificatorAnnotation = Annotation::createAnnotationWithValue(
			AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION,
			$type
		);

		$getAnnotationsCallback = function (ReflectionProperty $property, string $annotationName): array {
			switch ($annotationName) {
				case 'get':
					return [Annotation::createAnnotationWithFields('get', [
						new AnnotationField(AnnotationMetadataSource::METHOD_PARAM_NAME, 'test'),
					])];
				case 'set':
					return [];
			}
		};

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects($this->once())
			->method('getSentry')
			->with($sentryIdentificator)
			->will($this->returnValue(new SimpleType()));

		$annotationProvider = $this->createMock(AnnotationProvider::class);
		$annotationProvider
			->expects($this->once())
			->method('getPropertyAnnotation')
			->with($classReflection->getProperty($propertyName), $this->isType('string'))
			->will($this->returnValue($sentryIdentificatorAnnotation));
		$annotationProvider
			->expects($this->exactly(2))
			->method('getPropertyAnnotations')
			->will($this->returnCallback($getAnnotationsCallback));

		$metadataSource = new AnnotationMetadataSource(
			$sentryFactory,
			new SentryIdentificatorParser(),
			$annotationProvider
		);
		$classMetadata = $metadataSource->getMetadataForClass($classReflection);

		$this->assertSame($className, $classMetadata->getName());
		$properties = $classMetadata->getProperties();
		$this->assertCount(1, $properties);
		$fooPropety = $properties[0];
		$sentryMethods = $fooPropety->getSentryMethods();
		$this->assertCount(1, $sentryMethods);
		$getMethod = $sentryMethods[0];
		$this->assertSame('test', $getMethod->getMethodName());
	}

	public function testCustomMethodVisibility(): void
	{
		$type = 'string';
		$className = FooClass::class;
		$propertyName = 'fooProperty';
		$classReflection = new ReflectionClass($className);
		$sentryIdentificator = new SentryIdentificator($className . '::' . $type);

		$sentryIdentificatorAnnotation = Annotation::createAnnotationWithValue(
			AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION,
			$type
		);

		$getAnnotationsCallback = function (ReflectionProperty $property, string $annotationName): array {
			switch ($annotationName) {
				case 'get':
					return [Annotation::createAnnotationWithFields('get', [
						new AnnotationField(AnnotationMetadataSource::METHOD_PARAM_VISIBILITY, Visibility::VISIBILITY_PRIVATE),
					])];
				case 'set':
					return [];
			}
		};

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects($this->once())
			->method('getSentry')
			->with($sentryIdentificator)
			->will($this->returnValue(new SimpleType()));

		$annotationProvider = $this->createMock(AnnotationProvider::class);
		$annotationProvider
			->expects($this->once())
			->method('getPropertyAnnotation')
			->with($classReflection->getProperty($propertyName), $this->isType('string'))
			->will($this->returnValue($sentryIdentificatorAnnotation));
		$annotationProvider
			->expects($this->exactly(2))
			->method('getPropertyAnnotations')
			->will($this->returnCallback($getAnnotationsCallback));

		$metadataSource = new AnnotationMetadataSource(
			$sentryFactory,
			new SentryIdentificatorParser(),
			$annotationProvider
		);
		$classMetadata = $metadataSource->getMetadataForClass($classReflection);

		$this->assertSame($className, $classMetadata->getName());
		$properties = $classMetadata->getProperties();
		$this->assertCount(1, $properties);
		$fooPropety = $properties[0];
		$sentryMethods = $fooPropety->getSentryMethods();
		$this->assertCount(1, $sentryMethods);
		$getMethod = $sentryMethods[0];
		$this->assertSame(Visibility::get(Visibility::VISIBILITY_PRIVATE), $getMethod->getMethodVisibility());
	}

	public function testGetMultipleMethods(): void
	{
		$type = 'string';
		$className = FooClass::class;
		$propertyName = 'fooProperty';
		$classReflection = new ReflectionClass($className);
		$sentryIdentificator = new SentryIdentificator($className . '::' . $type);

		$sentryIdentificatorAnnotation = Annotation::createAnnotationWithValue(
			AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION,
			$type
		);

		$getAnnotationsCallback = function (ReflectionProperty $property, string $annotationName): array {
			switch ($annotationName) {
				case 'get':
					return [
						Annotation::createAnnotationWithFields('get', []),
						Annotation::createAnnotationWithFields('get', [
							new AnnotationField(AnnotationMetadataSource::METHOD_PARAM_NAME, 'getFooPrivate'),
							new AnnotationField(AnnotationMetadataSource::METHOD_PARAM_VISIBILITY, Visibility::VISIBILITY_PRIVATE),
						]),
					];
				case 'set':
					return [
						Annotation::createAnnotationWithFields('set', []),
						Annotation::createAnnotationWithFields('set', [
							new AnnotationField(AnnotationMetadataSource::METHOD_PARAM_NAME, 'setFooPrivate'),
							new AnnotationField(AnnotationMetadataSource::METHOD_PARAM_VISIBILITY, Visibility::VISIBILITY_PRIVATE),
						]),
					];
			}
		};

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects($this->once())
			->method('getSentry')
			->with($sentryIdentificator)
			->will($this->returnValue(new SimpleType()));

		$annotationProvider = $this->createMock(AnnotationProvider::class);
		$annotationProvider
			->expects($this->once())
			->method('getPropertyAnnotation')
			->with($classReflection->getProperty($propertyName), $this->isType('string'))
			->will($this->returnValue($sentryIdentificatorAnnotation));
		$annotationProvider
			->expects($this->exactly(2))
			->method('getPropertyAnnotations')
			->will($this->returnCallback($getAnnotationsCallback));

		$metadataSource = new AnnotationMetadataSource(
			$sentryFactory,
			new SentryIdentificatorParser(),
			$annotationProvider
		);
		$classMetadata = $metadataSource->getMetadataForClass($classReflection);

		$this->assertSame($className, $classMetadata->getName());
		$properties = $classMetadata->getProperties();
		$this->assertCount(1, $properties);
		$fooPropety = $properties[0];
		$sentryMethods = $fooPropety->getSentryMethods();
		$this->assertCount(4, $sentryMethods);
	}

	public function testClassIsNotSentryAware(): void
	{
		$sentryFactory = $this->createMock(SentryFactory::class);

		$annotationProvider = $this->createMock(AnnotationProvider::class);

		$metedataSource = new AnnotationMetadataSource(
			$sentryFactory,
			new SentryIdentificatorParser(),
			$annotationProvider
		);

		$this->expectException(\Consistence\Sentry\MetadataSource\ClassMetadataCouldNotBeCreatedException::class);
		$this->expectExceptionMessage('SentryAware');

		$metedataSource->getMetadataForClass(new ReflectionClass($this));
	}

	public function testInvalidSentryIdentificator(): void
	{
		$classReflection = new ReflectionClass(FooClass::class);

		$sentryFactory = $this->createMock(SentryFactory::class);

		$sentryIdentificatorAnnotation = Annotation::createAnnotationWithValue(
			AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION,
			''
		);

		$annotationProvider = $this->createMock(AnnotationProvider::class);
		$annotationProvider
			->expects($this->once())
			->method('getPropertyAnnotation')
			->with($classReflection->getProperty('fooProperty'), AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION)
			->will($this->returnValue($sentryIdentificatorAnnotation));

		$metedataSource = new AnnotationMetadataSource(
			$sentryFactory,
			new SentryIdentificatorParser(),
			$annotationProvider
		);

		$classMetadata = $metedataSource->getMetadataForClass($classReflection);
		$this->assertEmpty($classMetadata->getProperties());
	}

	public function testMissingSentryIdentificator(): void
	{
		$classReflection = new ReflectionClass(FooClass::class);
		$propertyReflection = $classReflection->getProperty('fooProperty');

		$sentryFactory = $this->createMock(SentryFactory::class);

		$annotationProvider = $this->createMock(AnnotationProvider::class);
		$annotationProvider
			->expects($this->once())
			->method('getPropertyAnnotation')
			->with($propertyReflection, AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION)
			->will($this->throwException(new \Consistence\Annotation\AnnotationNotFoundException(
				AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION,
				$propertyReflection
			)));

		$metedataSource = new AnnotationMetadataSource(
			$sentryFactory,
			new SentryIdentificatorParser(),
			$annotationProvider
		);

		$classMetadata = $metedataSource->getMetadataForClass($classReflection);
		$this->assertEmpty($classMetadata->getProperties());
	}

	public function testNoSentryFound(): void
	{
		$classReflection = new ReflectionClass(FooClass::class);
		$sentryIdentificator = new SentryIdentificator(FooClass::class . '::Foo\Bar');

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects($this->once())
			->method('getSentry')
			->with($sentryIdentificator)
			->will($this->throwException(new \Consistence\Sentry\Factory\NoSentryForIdentificatorException($sentryIdentificator)));

		$sentryIdentificatorAnnotation = Annotation::createAnnotationWithValue(
			AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION,
			'Foo\Bar'
		);

		$annotationProvider = $this->createMock(AnnotationProvider::class);
		$annotationProvider
			->expects($this->once())
			->method('getPropertyAnnotation')
			->with($classReflection->getProperty('fooProperty'), AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION)
			->will($this->returnValue($sentryIdentificatorAnnotation));

		$metedataSource = new AnnotationMetadataSource(
			$sentryFactory,
			new SentryIdentificatorParser(),
			$annotationProvider
		);

		$classMetadata = $metedataSource->getMetadataForClass($classReflection);
		$this->assertEmpty($classMetadata->getProperties());
	}

}
