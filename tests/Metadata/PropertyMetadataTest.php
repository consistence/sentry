<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

use PHPUnit\Framework\Assert;

class PropertyMetadataTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate(): void
	{
		$targetClass = 'BarClass';
		$sentryIdentificator = new SentryIdentificator($targetClass);
		$sentryMethods = [
			new SentryMethod(
				new SentryAccess('get'),
				'fooMethod',
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			),
		];
		$bidirectionalAssociation = new BidirectionalAssociation(
			$targetClass,
			'barProperty',
			BidirectionalAssociationType::get(BidirectionalAssociationType::ONE),
			[
				new SentryMethod(
					new SentryAccess('get'),
					'barMethod',
					Visibility::get(Visibility::VISIBILITY_PUBLIC)
				),
			]
		);
		$property = new PropertyMetadata(
			'fooProperty',
			'FooClass',
			$targetClass,
			$sentryIdentificator,
			false,
			$sentryMethods,
			$bidirectionalAssociation
		);

		Assert::assertSame('fooProperty', $property->getName());
		Assert::assertSame('FooClass', $property->getClassName());
		Assert::assertSame($targetClass, $property->getType());
		Assert::assertSame($sentryIdentificator, $property->getSentryIdentificator());
		Assert::assertSame(false, $property->isNullable());
		Assert::assertSame($sentryMethods, $property->getSentryMethods());
		Assert::assertSame($bidirectionalAssociation, $property->getBidirectionalAssociation());
	}

	public function testCreateScalar(): void
	{
		$sentryIdentificator = new SentryIdentificator('int');
		$property = new PropertyMetadata(
			'fooProperty',
			'FooClass',
			'int',
			$sentryIdentificator,
			false,
			[],
			null
		);

		Assert::assertSame('fooProperty', $property->getName());
		Assert::assertSame('FooClass', $property->getClassName());
		Assert::assertSame('int', $property->getType());
		Assert::assertSame($sentryIdentificator, $property->getSentryIdentificator());
		Assert::assertSame(false, $property->isNullable());
		Assert::assertEmpty($property->getSentryMethods());
		Assert::assertNull($property->getBidirectionalAssociation());
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
		$property = new PropertyMetadata(
			'fooProperty',
			'FooClass',
			$targetClass,
			$sentryIdentificator,
			false,
			[
				$setMethod,
			],
			null
		);

		$this->expectException(\Consistence\Sentry\Metadata\NoSuitableMethodException::class);

		$property->getSentryMethodByAccessAndRequiredVisibility(
			new SentryAccess('set'),
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
	}

	public function testGetSentryMethodByNameAndRequiredVisibility(): void
	{
		$sentryIdentificator = new SentryIdentificator('string');
		$fooMethod = new SentryMethod(
			new SentryAccess('get'),
			'fooMethod',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$property = new PropertyMetadata(
			'fooProperty',
			'FooClass',
			'string',
			$sentryIdentificator,
			false,
			[$fooMethod],
			null
		);

		Assert::assertSame($fooMethod, $property->getSentryMethodByNameAndRequiredVisibility(
			'fooMethod',
			Visibility::get(Visibility::VISIBILITY_PRIVATE)
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
			Assert::fail();
		} catch (\Consistence\Sentry\Metadata\MethodNotFoundForPropertyException $e) {
			Assert::assertSame('FooClass', $e->getClassName());
			Assert::assertSame('fooProperty', $e->getPropertyName());
			Assert::assertSame('fooMethod', $e->getMethodName());
		}
	}

	public function testGetSentryMethodByNameAndRequiredVisibilityCaseInsensitive(): void
	{
		$sentryIdentificator = new SentryIdentificator('string');
		$fooMethod = new SentryMethod(
			new SentryAccess('get'),
			'fooMethod',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$property = new PropertyMetadata(
			'fooProperty',
			'FooClass',
			'string',
			$sentryIdentificator,
			false,
			[$fooMethod],
			null
		);

		Assert::assertSame($fooMethod, $property->getSentryMethodByNameAndRequiredVisibility(
			'FOOmethod',
			Visibility::get(Visibility::VISIBILITY_PRIVATE)
		));
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
