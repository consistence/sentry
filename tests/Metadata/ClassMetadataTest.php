<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

use PHPUnit\Framework\Assert;

class ClassMetadataTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate(): void
	{
		$properties = [
			new PropertyMetadata(
				'fooProperty',
				'FooClass',
				'int',
				new SentryIdentificator('int'),
				false,
				[],
				null
			),
		];

		$classMetadata = new ClassMetadata(
			'FooClass',
			$properties
		);
		Assert::assertSame('FooClass', $classMetadata->getName());
		Assert::assertSame($properties, $classMetadata->getProperties());
	}

	public function testGetSentryMethodByNameAndRequiredVisibility(): void
	{
		$sentryIdentificator = new SentryIdentificator('string');
		$fooMethod = new SentryMethod(
			new SentryAccess('get'),
			'fooMethod',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$fooProperty = new PropertyMetadata(
			'fooProperty',
			'FooClass',
			'string',
			$sentryIdentificator,
			false,
			[$fooMethod],
			null
		);
		$classMetadata = new ClassMetadata(
			'FooClass',
			[$fooProperty]
		);

		$searchResult = $classMetadata->getSentryMethodByNameAndRequiredVisibility(
			'fooMethod',
			Visibility::get(Visibility::VISIBILITY_PRIVATE)
		);
		Assert::assertInstanceOf(SentryMethodSearchResult::class, $searchResult);
		Assert::assertSame($fooProperty, $searchResult->getProperty());
		Assert::assertSame($fooMethod, $searchResult->getSentryMethod());
	}

	public function testGetSentryMethodByNameAndRequiredVisibilityMethodNotFound(): void
	{
		$fooProperty = new PropertyMetadata(
			'fooProperty',
			'FooClass',
			'string',
			new SentryIdentificator('string'),
			false,
			[],
			null
		);
		$classMetadata = new ClassMetadata(
			'FooClass',
			[$fooProperty]
		);

		try {
			$classMetadata->getSentryMethodByNameAndRequiredVisibility(
				'fooMethod',
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			);
			Assert::fail();
		} catch (\Consistence\Sentry\Metadata\MethodNotFoundException $e) {
			Assert::assertSame('FooClass', $e->getClassName());
			Assert::assertSame('fooMethod', $e->getMethodName());
		}
	}

	public function testGetPropertyByName(): void
	{
		$propertyName = 'fooProperty';

		$fooProperty = new PropertyMetadata(
			$propertyName,
			'FooClass',
			'int',
			new SentryIdentificator('int'),
			false,
			[],
			null
		);

		$classMetadata = new ClassMetadata('FooClass', [$fooProperty]);
		Assert::assertSame($fooProperty, $classMetadata->getPropertyByName($propertyName));
	}

	public function testGetPropertyByNameNotFound(): void
	{
		$className = 'FooClass';
		$classMetadata = new ClassMetadata($className, []);
		$propertyName = 'foo';

		try {
			$classMetadata->getPropertyByName($propertyName);
			Assert::fail();
		} catch (\Consistence\Sentry\Metadata\PropertyNotFoundException $e) {
			Assert::assertSame($className, $e->getClassName());
			Assert::assertSame($propertyName, $e->getPropertyName());
		}
	}

}
