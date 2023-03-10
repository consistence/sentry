<?php

declare(strict_types = 1);

namespace Consistence\Sentry\MetadataSource\Annotation;

use Closure;
use Consistence\Annotation\Annotation;
use Consistence\Annotation\AnnotationField;
use Consistence\Annotation\AnnotationProvider;
use Consistence\Sentry\Factory\SentryFactory;
use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryAccess;
use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\Metadata\Visibility;
use Consistence\Sentry\MetadataSource\FooClass;
use Consistence\Sentry\SentryIdentificatorParser\SentryIdentificatorParser;
use Consistence\Sentry\Type\SimpleType;
use Consistence\Type\ArrayType\ArrayType;
use Generator;
use PHPUnit\Framework\Assert;
use ReflectionClass;
use ReflectionProperty;

class AnnotationMetadataSourceTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function getMetadataForClassDataProvider(): Generator
	{
		yield 'get' => (function (): array {
			$className = FooClass::class;
			$propertyName = 'fooProperty';
			$propertyType = 'string';

			return [
				'className' => $className,
				'propertyName' => $propertyName,
				'propertyType' => $propertyType,
				'getAnnotationsCallback' => function (ReflectionProperty $property, string $annotationName): array {
					switch ($annotationName) {
						case 'get':
							return [Annotation::createAnnotationWithFields('get', [])];
						case 'set':
							return [];
					}
				},
				'expectedPropertyMetadata' => new PropertyMetadata(
					$propertyName,
					$className,
					$propertyType,
					new SentryIdentificator(sprintf('%s::%s', $className, $propertyType)),
					false,
					[
						new SentryMethod(
							new SentryAccess('get'),
							'getFooProperty',
							Visibility::get(Visibility::VISIBILITY_PUBLIC)
						),
					],
					null
				),
			];
		})();

		yield 'custom method name' => (function (): array {
			$className = FooClass::class;
			$propertyName = 'fooProperty';
			$propertyType = 'string';

			return [
				'className' => $className,
				'propertyName' => $propertyName,
				'propertyType' => $propertyType,
				'getAnnotationsCallback' => function (ReflectionProperty $property, string $annotationName): array {
					switch ($annotationName) {
						case 'get':
							return [Annotation::createAnnotationWithFields('get', [
								new AnnotationField(AnnotationMetadataSource::METHOD_PARAM_NAME, 'test'),
							])];
						case 'set':
							return [];
					}
				},
				'expectedPropertyMetadata' => new PropertyMetadata(
					$propertyName,
					$className,
					$propertyType,
					new SentryIdentificator(sprintf('%s::%s', $className, $propertyType)),
					false,
					[
						new SentryMethod(
							new SentryAccess('get'),
							'test',
							Visibility::get(Visibility::VISIBILITY_PUBLIC)
						),
					],
					null
				),
			];
		})();

		yield 'custom method visibility' => (function (): array {
			$className = FooClass::class;
			$propertyName = 'fooProperty';
			$propertyType = 'string';

			return [
				'className' => $className,
				'propertyName' => $propertyName,
				'propertyType' => $propertyType,
				'getAnnotationsCallback' => function (ReflectionProperty $property, string $annotationName): array {
					switch ($annotationName) {
						case 'get':
							return [Annotation::createAnnotationWithFields('get', [
								new AnnotationField(AnnotationMetadataSource::METHOD_PARAM_VISIBILITY, Visibility::VISIBILITY_PRIVATE),
							])];
						case 'set':
							return [];
					}
				},
				'expectedPropertyMetadata' => new PropertyMetadata(
					$propertyName,
					$className,
					$propertyType,
					new SentryIdentificator(sprintf('%s::%s', $className, $propertyType)),
					false,
					[
						new SentryMethod(
							new SentryAccess('get'),
							'getFooProperty',
							Visibility::get(Visibility::VISIBILITY_PRIVATE)
						),
					],
					null
				),
			];
		})();

		yield 'multiple methods' => (function (): array {
			$className = FooClass::class;
			$propertyName = 'fooProperty';
			$propertyType = 'string';

			return [
				'className' => $className,
				'propertyName' => $propertyName,
				'propertyType' => $propertyType,
				'getAnnotationsCallback' => function (ReflectionProperty $property, string $annotationName): array {
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
				},
				'expectedPropertyMetadata' => new PropertyMetadata(
					$propertyName,
					$className,
					$propertyType,
					new SentryIdentificator(sprintf('%s::%s', $className, $propertyType)),
					false,
					[
						new SentryMethod(
							new SentryAccess('get'),
							'getFooProperty',
							Visibility::get(Visibility::VISIBILITY_PUBLIC)
						),
						new SentryMethod(
							new SentryAccess('get'),
							'getFooPrivate',
							Visibility::get(Visibility::VISIBILITY_PRIVATE)
						),
						new SentryMethod(
							new SentryAccess('set'),
							'setFooProperty',
							Visibility::get(Visibility::VISIBILITY_PUBLIC)
						),
						new SentryMethod(
							new SentryAccess('set'),
							'setFooPrivate',
							Visibility::get(Visibility::VISIBILITY_PRIVATE)
						),
					],
					null
				),
			];
		})();
	}

	/**
	 * @dataProvider getMetadataForClassDataProvider
	 *
	 * @param string $className
	 * @param string $propertyName
	 * @param string $propertyType
	 * @param \Closure $getAnnotationsCallback
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $expectedPropertyMetadata
	 */
	public function testGetMetadataForClass(
		string $className,
		string $propertyName,
		string $propertyType,
		Closure $getAnnotationsCallback,
		PropertyMetadata $expectedPropertyMetadata
	): void
	{
		$classReflection = new ReflectionClass($className);
		$sentryIdentificator = new SentryIdentificator($className . '::' . $propertyType);

		$sentryIdentificatorAnnotation = Annotation::createAnnotationWithValue(
			AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION,
			$propertyType
		);

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects(self::once())
			->method('getSentry')
			->with($sentryIdentificator)
			->will(self::returnValue(new SimpleType()));

		$annotationProvider = $this->createMock(AnnotationProvider::class);
		$annotationProvider
			->expects(self::once())
			->method('getPropertyAnnotation')
			->with($classReflection->getProperty($propertyName), Assert::isType('string'))
			->will(self::returnValue($sentryIdentificatorAnnotation));
		$annotationProvider
			->expects(self::exactly(2))
			->method('getPropertyAnnotations')
			->will(self::returnCallback($getAnnotationsCallback));

		$metadataSource = new AnnotationMetadataSource(
			$sentryFactory,
			new SentryIdentificatorParser(),
			$annotationProvider
		);

		$classMetadata = $metadataSource->getMetadataForClass($classReflection);
		Assert::assertSame($expectedPropertyMetadata->getClassName(), $classMetadata->getName());

		$properties = $classMetadata->getProperties();
		Assert::assertCount(1, $properties);

		$property = $properties[0];
		Assert::assertSame($expectedPropertyMetadata->getName(), $property->getName());
		Assert::assertSame($expectedPropertyMetadata->getClassName(), $property->getClassName());
		Assert::assertSame($expectedPropertyMetadata->getType(), $property->getType());
		Assert::assertTrue($expectedPropertyMetadata->getSentryIdentificator()->equals($property->getSentryIdentificator()));
		Assert::assertSame($expectedPropertyMetadata->isNullable(), $property->isNullable());
		Assert::assertSame($expectedPropertyMetadata->getBidirectionalAssociation(), $property->getBidirectionalAssociation());

		foreach ($expectedPropertyMetadata->getSentryMethods() as $expectedSentryMethod) {
			Assert::assertTrue(ArrayType::containsValueByValueCallback(
				$property->getSentryMethods(),
				static function (SentryMethod $sentryMethod) use ($expectedSentryMethod): bool {
					return $expectedSentryMethod->getMethodName() === $sentryMethod->getMethodName()
						&& $expectedSentryMethod->getSentryAccess()->equals($sentryMethod->getSentryAccess())
						&& $expectedSentryMethod->getMethodVisibility()->equals($sentryMethod->getMethodVisibility());
				}
			));
		}
		Assert::assertCount(count($expectedPropertyMetadata->getSentryMethods()), $property->getSentryMethods());
	}

	public function testClassIsNotSentryAware(): void
	{
		$sentryFactory = $this->createMock(SentryFactory::class);

		$annotationProvider = $this->createMock(AnnotationProvider::class);

		$metadataSource = new AnnotationMetadataSource(
			$sentryFactory,
			new SentryIdentificatorParser(),
			$annotationProvider
		);

		$reflectionClass = new ReflectionClass($this);

		try {
			$metadataSource->getMetadataForClass($reflectionClass);
			Assert::fail('Exception expected');
		} catch (\Consistence\Sentry\MetadataSource\ClassMetadataCouldNotBeCreatedException $e) {
			Assert::assertSame($reflectionClass, $e->getClassReflection());
			Assert::assertStringContainsString('SentryAware', $e->getMessage());
		}
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
			->expects(self::once())
			->method('getPropertyAnnotation')
			->with($classReflection->getProperty('fooProperty'), AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION)
			->will(self::returnValue($sentryIdentificatorAnnotation));

		$metadataSource = new AnnotationMetadataSource(
			$sentryFactory,
			new SentryIdentificatorParser(),
			$annotationProvider
		);

		$classMetadata = $metadataSource->getMetadataForClass($classReflection);
		Assert::assertEmpty($classMetadata->getProperties());
	}

	public function testMissingSentryIdentificator(): void
	{
		$classReflection = new ReflectionClass(FooClass::class);
		$propertyReflection = $classReflection->getProperty('fooProperty');

		$sentryFactory = $this->createMock(SentryFactory::class);

		$annotationProvider = $this->createMock(AnnotationProvider::class);
		$annotationProvider
			->expects(self::once())
			->method('getPropertyAnnotation')
			->with($propertyReflection, AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION)
			->will(self::throwException(new \Consistence\Annotation\AnnotationNotFoundException(
				AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION,
				$propertyReflection
			)));

		$metadataSource = new AnnotationMetadataSource(
			$sentryFactory,
			new SentryIdentificatorParser(),
			$annotationProvider
		);

		$classMetadata = $metadataSource->getMetadataForClass($classReflection);
		Assert::assertEmpty($classMetadata->getProperties());
	}

	public function testNoSentryFound(): void
	{
		$classReflection = new ReflectionClass(FooClass::class);
		$sentryIdentificator = new SentryIdentificator(FooClass::class . '::Foo\Bar');

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects(self::once())
			->method('getSentry')
			->with($sentryIdentificator)
			->will(self::throwException(new \Consistence\Sentry\Factory\NoSentryForIdentificatorException($sentryIdentificator)));

		$sentryIdentificatorAnnotation = Annotation::createAnnotationWithValue(
			AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION,
			'Foo\Bar'
		);

		$annotationProvider = $this->createMock(AnnotationProvider::class);
		$annotationProvider
			->expects(self::once())
			->method('getPropertyAnnotation')
			->with($classReflection->getProperty('fooProperty'), AnnotationMetadataSource::IDENTIFICATOR_ANNOTATION)
			->will(self::returnValue($sentryIdentificatorAnnotation));

		$metadataSource = new AnnotationMetadataSource(
			$sentryFactory,
			new SentryIdentificatorParser(),
			$annotationProvider
		);

		$classMetadata = $metadataSource->getMetadataForClass($classReflection);
		Assert::assertEmpty($classMetadata->getProperties());
	}

}
