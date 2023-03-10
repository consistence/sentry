<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

use Generator;
use PHPUnit\Framework\Assert;

class PropertyMetadataTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function createDataProvider(): Generator
	{
		yield 'create object' => (function (): array {
			$targetType = 'BarClass';

			return [
				'name' => 'fooProperty',
				'className' => 'FooClass',
				'targetType' => $targetType,
				'sentryIdentificator' => new SentryIdentificator($targetType),
				'nullable' => false,
				'sentryMethods' => [
					new SentryMethod(
						new SentryAccess('get'),
						'fooMethod',
						Visibility::get(Visibility::VISIBILITY_PUBLIC)
					),
				],
				'bidirectionalAssociation' => new BidirectionalAssociation(
					$targetType,
					'barProperty',
					BidirectionalAssociationType::get(BidirectionalAssociationType::ONE),
					[
						new SentryMethod(
							new SentryAccess('get'),
							'barMethod',
							Visibility::get(Visibility::VISIBILITY_PUBLIC)
						),
					]
				),
			];
		})();

		yield 'create scalar' => (function (): array {
			$targetType = 'int';

			return [
				'name' => 'fooProperty',
				'className' => 'FooClass',
				'targetType' => $targetType,
				'sentryIdentificator' => new SentryIdentificator($targetType),
				'nullable' => false,
				'sentryMethods' => [],
				'bidirectionalAssociation' => null,
			];
		})();
	}

	/**
	 * @dataProvider createDataProvider
	 *
	 * @param string $name
	 * @param string $className
	 * @param string $targetType
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $sentryIdentificator
	 * @param bool $nullable
	 * @param \Consistence\Sentry\Metadata\SentryMethod[] $sentryMethods
	 * @param \Consistence\Sentry\Metadata\BidirectionalAssociation|null $bidirectionalAssociation
	 * @return void
	 */
	public function testCreate(
		string $name,
		string $className,
		string $targetType,
		SentryIdentificator $sentryIdentificator,
		bool $nullable,
		array $sentryMethods,
		?BidirectionalAssociation $bidirectionalAssociation
	): void
	{
		$property = new PropertyMetadata(
			$name,
			$className,
			$targetType,
			$sentryIdentificator,
			$nullable,
			$sentryMethods,
			$bidirectionalAssociation
		);

		Assert::assertSame($name, $property->getName());
		Assert::assertSame($className, $property->getClassName());
		Assert::assertSame($targetType, $property->getType());
		Assert::assertSame($sentryIdentificator, $property->getSentryIdentificator());
		Assert::assertSame($nullable, $property->isNullable());
		Assert::assertSame($sentryMethods, $property->getSentryMethods());
		Assert::assertSame($bidirectionalAssociation, $property->getBidirectionalAssociation());
	}

	public function testGetSentryMethodByAccess(): void
	{
		$targetClass = 'BarClass';
		$sentryIdentificator = new SentryIdentificator($targetClass);
		$getMethod = new SentryMethod(
			new SentryAccess('get'),
			'getFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$property = new PropertyMetadata(
			'fooProperty',
			'FooClass',
			$targetClass,
			$sentryIdentificator,
			false,
			[
				$getMethod,
				$setMethod,
			],
			null
		);

		Assert::assertSame($setMethod, $property->getSentryMethodByAccessAndRequiredVisibility(
			new SentryAccess('set'),
			Visibility::get(Visibility::VISIBILITY_PRIVATE)
		));
	}

	public function testGetSentryMethodByAccessNotFound(): void
	{
		$targetClass = 'BarClass';
		$sentryIdentificator = new SentryIdentificator($targetClass);
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setFoo',
			Visibility::get(Visibility::VISIBILITY_PRIVATE)
		);
		$className = 'FooClass';
		$propertyName = 'fooProperty';
		$property = new PropertyMetadata(
			$propertyName,
			$className,
			$targetClass,
			$sentryIdentificator,
			false,
			[
				$setMethod,
			],
			null
		);

		$sentryAccess = new SentryAccess('set');

		try {
			$property->getSentryMethodByAccessAndRequiredVisibility(
				$sentryAccess,
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			);
			Assert::fail('Exception expected');
		} catch (\Consistence\Sentry\Metadata\NoSuitableMethodException $e) {
			Assert::assertSame($className, $e->getClassName());
			Assert::assertSame($propertyName, $e->getPropertyName());
			Assert::assertSame($sentryAccess, $e->getSentryAccess());
		}
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function getSentryMethodByNameAndRequiredVisibilityDataProvider(): Generator
	{
		yield 'case sensitive' => (function (): array {
			$fooMethod = new SentryMethod(
				new SentryAccess('get'),
				'fooMethod',
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			);

			return [
				'expectedSentryMethod' => $fooMethod,
				'propertyMetadata' => new PropertyMetadata(
					'fooProperty',
					'FooClass',
					'string',
					new SentryIdentificator('string'),
					false,
					[$fooMethod],
					null
				),
				'methodName' => 'fooMethod',
				'requiredVisibility' => Visibility::get(Visibility::VISIBILITY_PRIVATE),
			];
		})();

		yield 'case insensitive' => (function (): array {
			$fooMethod = new SentryMethod(
				new SentryAccess('get'),
				'fooMethod',
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			);

			return [
				'expectedSentryMethod' => $fooMethod,
				'propertyMetadata' => new PropertyMetadata(
					'fooProperty',
					'FooClass',
					'string',
					new SentryIdentificator('string'),
					false,
					[$fooMethod],
					null
				),
				'methodName' => 'FOOMethod',
				'requiredVisibility' => Visibility::get(Visibility::VISIBILITY_PRIVATE),
			];
		})();
	}

	/**
	 * @dataProvider getSentryMethodByNameAndRequiredVisibilityDataProvider
	 *
	 * @param \Consistence\Sentry\Metadata\SentryMethod $expectedSentryMethod
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $propertyMetadata
	 * @param string $methodName
	 * @param \Consistence\Sentry\Metadata\Visibility $requiredVisibility
	 */
	public function testGetSentryMethodByNameAndRequiredVisibility(
		SentryMethod $expectedSentryMethod,
		PropertyMetadata $propertyMetadata,
		string $methodName,
		Visibility $requiredVisibility
	): void
	{
		Assert::assertSame($expectedSentryMethod, $propertyMetadata->getSentryMethodByNameAndRequiredVisibility(
			$methodName,
			$requiredVisibility
		));
	}

	public function testGetSentryMethodByNameAndRequiredVisibilityMethodNotFound(): void
	{
		$property = new PropertyMetadata(
			'fooProperty',
			'FooClass',
			'string',
			new SentryIdentificator('string'),
			false,
			[],
			null
		);

		try {
			$property->getSentryMethodByNameAndRequiredVisibility(
				'fooMethod',
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			);
			Assert::fail('Exception expected');
		} catch (\Consistence\Sentry\Metadata\MethodNotFoundForPropertyException $e) {
			Assert::assertSame('FooClass', $e->getClassName());
			Assert::assertSame('fooProperty', $e->getPropertyName());
			Assert::assertSame('fooMethod', $e->getMethodName());
		}
	}

	public function testGetDefinedSentryAccess(): void
	{
		$targetClass = 'BarClass';
		$sentryIdentificator = new SentryIdentificator($targetClass);
		$privateSetMethod = new SentryMethod(
			new SentryAccess('set'),
			'setFoo',
			Visibility::get(Visibility::VISIBILITY_PRIVATE)
		);
		$publicSetMethod = new SentryMethod(
			new SentryAccess('set'),
			'setFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$property = new PropertyMetadata(
			'fooProperty',
			'FooClass',
			$targetClass,
			$sentryIdentificator,
			false,
			[
				$privateSetMethod,
				$publicSetMethod,
			],
			null
		);

		Assert::assertEquals([new SentryAccess('set')], $property->getDefinedSentryAccess());
	}

}
