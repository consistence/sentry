<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

class ClassMetadataTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate()
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
		$this->assertSame('FooClass', $classMetadata->getName());
		$this->assertSame($properties, $classMetadata->getProperties());
	}

	public function testGetSentryMethodByNameAndRequiredVisibility()
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
		$this->assertInstanceOf(SentryMethodSearchResult::class, $searchResult);
		$this->assertSame($fooProperty, $searchResult->getProperty());
		$this->assertSame($fooMethod, $searchResult->getSentryMethod());
	}

	public function testGetSentryMethodByNameAndRequiredVisibilityMethodNotFound()
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
			$this->fail();
		} catch (\Consistence\Sentry\Metadata\MethodNotFoundException $e) {
			$this->assertSame('FooClass', $e->getClassName());
			$this->assertSame('fooMethod', $e->getMethodName());
		}
	}

	public function testGetPropertyByName()
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
		$this->assertSame($fooProperty, $classMetadata->getPropertyByName($propertyName));
	}

	public function testGetPropertyByNameNotFound()
	{
		$className = 'FooClass';
		$classMetadata = new ClassMetadata($className, []);
		$propertyName = 'foo';

		try {
			$classMetadata->getPropertyByName($propertyName);
			$this->fail();
		} catch (\Consistence\Sentry\Metadata\PropertyNotFoundException $e) {
			$this->assertSame($className, $e->getClassName());
			$this->assertSame($propertyName, $e->getPropertyName());
		}
	}

}
