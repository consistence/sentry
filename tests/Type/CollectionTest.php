<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryAccess;
use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\Metadata\Visibility;
use PHPUnit\Framework\Assert;

class CollectionTest extends \PHPUnit\Framework\TestCase
{

	public function testSupportedAccess(): void
	{
		$collection = new CollectionType();

		Assert::assertEquals([
			new SentryAccess('get'),
			new SentryAccess('set'),
			new SentryAccess('add'),
			new SentryAccess('remove'),
			new SentryAccess('contains'),
		], $collection->getSupportedAccess());
	}

	public function testDefaultMethodNames(): void
	{
		$collection = new CollectionType();

		Assert::assertSame('getChildren', $collection->getDefaultMethodName(new SentryAccess('get'), 'children'));
		Assert::assertSame('setChildren', $collection->getDefaultMethodName(new SentryAccess('set'), 'children'));
		Assert::assertSame('addChild', $collection->getDefaultMethodName(new SentryAccess('add'), 'children'));
		Assert::assertSame('removeChild', $collection->getDefaultMethodName(new SentryAccess('remove'), 'children'));
		Assert::assertSame('containsChild', $collection->getDefaultMethodName(new SentryAccess('contains'), 'children'));
	}

	public function testGenerateGet(): void
	{
		$collection = new CollectionType();
		$getMethod = new SentryMethod(
			new SentryAccess('get'),
			'getFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'int',
			new SentryIdentificator('int[]'),
			false,
			[
				$getMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated int collection getter
	 *
	 * @return int[]
	 */
	public function getFoo()
	{
		return $this->children;
	}';
		Assert::assertSame($method, $collection->generateMethod($propertyMetadata, $getMethod));
	}

	public function testGenerateSet(): void
	{
		$collection = new CollectionType();
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'int',
			new SentryIdentificator('int[]'),
			false,
			[
				$setMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated int collection setter
	 *
	 * @param int[] $newValues
	 */
	public function setFoo($newValues)
	{
		\Consistence\Type\Type::checkType($newValues, \'array\');
		$collection =& $this->children;
		$collection = [];
		foreach ($newValues as $el) {
			\Consistence\Type\Type::checkType($el, \'int\');
			if (!\Consistence\Type\ArrayType\ArrayType::containsValue($collection, $el)) {
				$collection[] = $el;
			}
		}
	}';
		Assert::assertSame($method, $collection->generateMethod($propertyMetadata, $setMethod));
	}

	public function testGenerateContains(): void
	{
		$collection = new CollectionType();
		$containsMethod = new SentryMethod(
			new SentryAccess('contains'),
			'containsFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'int',
			new SentryIdentificator('int[]'),
			false,
			[
				$containsMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated int collection contains
	 *
	 * @param int $value
	 * @return bool
	 */
	public function containsFoo($value)
	{
		\Consistence\Type\Type::checkType($value, \'int\');
		return \Consistence\Type\ArrayType\ArrayType::containsValue($this->children, $value);
	}';
		Assert::assertSame($method, $collection->generateMethod($propertyMetadata, $containsMethod));
	}

	public function testGenerateAdd(): void
	{
		$collection = new CollectionType();
		$addMethod = new SentryMethod(
			new SentryAccess('add'),
			'addFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'int',
			new SentryIdentificator('int[]'),
			false,
			[
				$addMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated int collection add
	 *
	 * @param int $newValue
	 * @return bool was element really added?
	 */
	public function addFoo($newValue)
	{
		\Consistence\Type\Type::checkType($newValue, \'int\');
		$collection =& $this->children;
		if (!\Consistence\Type\ArrayType\ArrayType::containsValue($collection, $newValue)) {
			$collection[] = $newValue;

			return true;
		}

		return false;
	}';
		Assert::assertSame($method, $collection->generateMethod($propertyMetadata, $addMethod));
	}

	public function testGenerateRemove(): void
	{
		$collection = new CollectionType();
		$removeMethod = new SentryMethod(
			new SentryAccess('remove'),
			'removeFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'int',
			new SentryIdentificator('int[]'),
			false,
			[
				$removeMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated int collection remove
	 *
	 * @param int $value
	 * @return bool was element really removed?
	 */
	public function removeFoo($value)
	{
		\Consistence\Type\Type::checkType($value, \'int\');
		return \Consistence\Type\ArrayType\ArrayType::removeValue($this->children, $value);
	}';
		Assert::assertSame($method, $collection->generateMethod($propertyMetadata, $removeMethod));
	}

}
