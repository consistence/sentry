<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryAccess;
use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\Metadata\Visibility;

class CollectionTest extends \PHPUnit\Framework\TestCase
{

	public function testSupportedAccess()
	{
		$collection = new CollectionType();

		$this->assertEquals([
			new SentryAccess('get'),
			new SentryAccess('set'),
			new SentryAccess('add'),
			new SentryAccess('remove'),
			new SentryAccess('contains'),
		], $collection->getSupportedAccess());
	}

	public function testDefaultMethodNames()
	{
		$collection = new CollectionType();

		$this->assertSame('getChildren', $collection->getDefaultMethodName(new SentryAccess('get'), 'children'));
		$this->assertSame('setChildren', $collection->getDefaultMethodName(new SentryAccess('set'), 'children'));
		$this->assertSame('addChild', $collection->getDefaultMethodName(new SentryAccess('add'), 'children'));
		$this->assertSame('removeChild', $collection->getDefaultMethodName(new SentryAccess('remove'), 'children'));
		$this->assertSame('containsChild', $collection->getDefaultMethodName(new SentryAccess('contains'), 'children'));
	}

	public function testProcessGetAndSet()
	{
		$collection = new CollectionType();
		$getMethod = new SentryMethod(
			new SentryAccess('get'),
			'getChildren',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setChildren',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'string',
			new SentryIdentificator('string[]'),
			false,
			[
				$getMethod,
				$setMethod,
			],
			null
		);
		$foo = new FooClass();

		$parameters = ['foo', 'bar'];
		$this->assertEmpty($collection->processMethod($propertyMetadata, $foo, $getMethod, []));
		$this->assertNull($collection->processMethod($propertyMetadata, $foo, $setMethod, [$parameters]));
		$this->assertEquals($parameters, $collection->processMethod($propertyMetadata, $foo, $getMethod, []));
	}

	public function testProcessSetChangeValues()
	{
		$collection = new CollectionType();
		$getMethod = new SentryMethod(
			new SentryAccess('get'),
			'getChildren',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setChildren',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'string',
			new SentryIdentificator('string[]'),
			false,
			[
				$getMethod,
				$setMethod,
			],
			null
		);
		$foo = new FooClass();

		$parameters = ['foo', 'bar'];
		$this->assertNull($collection->processMethod($propertyMetadata, $foo, $setMethod, [$parameters]));
		$this->assertEquals($parameters, $collection->processMethod($propertyMetadata, $foo, $getMethod, []));

		$newParameters = ['test'];
		$this->assertNull($collection->processMethod($propertyMetadata, $foo, $setMethod, [$newParameters]));
		$this->assertContains('test', $collection->processMethod($propertyMetadata, $foo, $getMethod, []));
		$this->assertNotContains('foo', $collection->processMethod($propertyMetadata, $foo, $getMethod, []));
		$this->assertNotContains('bar', $collection->processMethod($propertyMetadata, $foo, $getMethod, []));
	}

	public function testProcessSetNotArrayParams()
	{
		$collection = new CollectionType();
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setChildren',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'string',
			new SentryIdentificator('string[]'),
			false,
			[
				$setMethod,
			],
			null
		);
		$foo = new FooClass();

		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('array expected');

		$collection->processMethod($propertyMetadata, $foo, $setMethod, ['foo']);
	}

	public function testProcessSetWrongType()
	{
		$collection = new CollectionType();
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setChildren',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'string',
			new SentryIdentificator('string[]'),
			false,
			[
				$setMethod,
			],
			null
		);
		$foo = new FooClass();

		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('string expected');

		$collection->processMethod($propertyMetadata, $foo, $setMethod, [[1, 2]]);
	}

	public function testProcessContains()
	{
		$collection = new CollectionType();
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setChildren',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$containsMethod = new SentryMethod(
			new SentryAccess('contains'),
			'containsChild',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'string',
			new SentryIdentificator('string[]'),
			false,
			[
				$setMethod,
				$containsMethod,
			],
			null
		);
		$foo = new FooClass();
		$this->assertNull($collection->processMethod($propertyMetadata, $foo, $setMethod, [['foo', 'bar']]));

		$this->assertTrue($collection->processMethod($propertyMetadata, $foo, $containsMethod, ['foo']));
		$this->assertTrue($collection->processMethod($propertyMetadata, $foo, $containsMethod, ['bar']));
		$this->assertFalse($collection->processMethod($propertyMetadata, $foo, $containsMethod, ['xxx']));
	}

	public function testProcessAdd()
	{
		$collection = new CollectionType();
		$addMethod = new SentryMethod(
			new SentryAccess('add'),
			'addChildren',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$containsMethod = new SentryMethod(
			new SentryAccess('contains'),
			'containsChild',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'string',
			new SentryIdentificator('string[]'),
			false,
			[
				$addMethod,
				$containsMethod,
			],
			null
		);
		$foo = new FooClass();

		$this->assertFalse($collection->processMethod($propertyMetadata, $foo, $containsMethod, ['foo']));
		$this->assertTrue($collection->processMethod($propertyMetadata, $foo, $addMethod, ['foo']));
		$this->assertTrue($collection->processMethod($propertyMetadata, $foo, $containsMethod, ['foo']));
	}

	public function testProcessAddOnlyOnce()
	{
		$collection = new CollectionType();
		$addMethod = new SentryMethod(
			new SentryAccess('add'),
			'addChildren',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$getMethod = new SentryMethod(
			new SentryAccess('get'),
			'getChildren',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'string',
			new SentryIdentificator('string[]'),
			false,
			[
				$addMethod,
				$getMethod,
			],
			null
		);
		$foo = new FooClass();

		$this->assertEmpty($collection->processMethod($propertyMetadata, $foo, $getMethod, []));
		$this->assertTrue($collection->processMethod($propertyMetadata, $foo, $addMethod, ['foo']));
		$this->assertFalse($collection->processMethod($propertyMetadata, $foo, $addMethod, ['foo']));
		$this->assertCount(1, $collection->processMethod($propertyMetadata, $foo, $getMethod, []));
	}

	public function testProcessRemove()
	{
		$collection = new CollectionType();
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setChildren',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$removeMethod = new SentryMethod(
			new SentryAccess('remove'),
			'removeChildren',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$containsMethod = new SentryMethod(
			new SentryAccess('contains'),
			'containsChild',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'string',
			new SentryIdentificator('string[]'),
			false,
			[
				$setMethod,
				$removeMethod,
				$containsMethod,
			],
			null
		);
		$foo = new FooClass();

		$this->assertNull($collection->processMethod($propertyMetadata, $foo, $setMethod, [['foo', 'bar']]));
		$this->assertTrue($collection->processMethod($propertyMetadata, $foo, $removeMethod, ['foo']));
		$this->assertFalse($collection->processMethod($propertyMetadata, $foo, $containsMethod, ['foo']));
		$this->assertTrue($collection->processMethod($propertyMetadata, $foo, $containsMethod, ['bar']));
	}

	public function testProcessRemoveMissing()
	{
		$collection = new CollectionType();
		$removeMethod = new SentryMethod(
			new SentryAccess('remove'),
			'removeChildren',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'children',
			FooClass::class,
			'string',
			new SentryIdentificator('string[]'),
			false,
			[
				$removeMethod,
			],
			null
		);
		$foo = new FooClass();

		$this->assertFalse($collection->processMethod($propertyMetadata, $foo, $removeMethod, ['foo']));
	}

	public function testGenerateGet()
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
			'integer',
			new SentryIdentificator('integer[]'),
			false,
			[
				$getMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated integer collection getter
	 *
	 * @return integer[]
	 */
	public function getFoo()
	{
		return $this->children;
	}';
		$this->assertSame($method, $collection->generateMethod($propertyMetadata, $getMethod));
	}

	public function testGenerateSet()
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
			'integer',
			new SentryIdentificator('integer[]'),
			false,
			[
				$setMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated integer collection setter
	 *
	 * @param integer[] $newValues
	 */
	public function setFoo($newValues)
	{
		\Consistence\Type\Type::checkType($newValues, \'array\');
		$collection =& $this->children;
		$collection = [];
		foreach ($newValues as $el) {
			\Consistence\Type\Type::checkType($el, \'integer\');
			if (!\Consistence\Type\ArrayType\ArrayType::containsValue($collection, $el)) {
				$collection[] = $el;
			}
		}
	}';
		$this->assertSame($method, $collection->generateMethod($propertyMetadata, $setMethod));
	}

	public function testGenerateContains()
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
			'integer',
			new SentryIdentificator('integer[]'),
			false,
			[
				$containsMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated integer collection contains
	 *
	 * @param integer $value
	 * @return boolean
	 */
	public function containsFoo($value)
	{
		\Consistence\Type\Type::checkType($value, \'integer\');
		return \Consistence\Type\ArrayType\ArrayType::containsValue($this->children, $value);
	}';
		$this->assertSame($method, $collection->generateMethod($propertyMetadata, $containsMethod));
	}

	public function testGenerateAdd()
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
			'integer',
			new SentryIdentificator('integer[]'),
			false,
			[
				$addMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated integer collection add
	 *
	 * @param integer $newValue
	 * @return boolean was element really added?
	 */
	public function addFoo($newValue)
	{
		\Consistence\Type\Type::checkType($newValue, \'integer\');
		$collection =& $this->children;
		if (!\Consistence\Type\ArrayType\ArrayType::containsValue($collection, $newValue)) {
			$collection[] = $newValue;

			return true;
		}

		return false;
	}';
		$this->assertSame($method, $collection->generateMethod($propertyMetadata, $addMethod));
	}

	public function testGenerateRemove()
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
			'integer',
			new SentryIdentificator('integer[]'),
			false,
			[
				$removeMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated integer collection remove
	 *
	 * @param integer $value
	 * @return boolean was element really removed?
	 */
	public function removeFoo($value)
	{
		\Consistence\Type\Type::checkType($value, \'integer\');
		return \Consistence\Type\ArrayType\ArrayType::removeValue($this->children, $value);
	}';
		$this->assertSame($method, $collection->generateMethod($propertyMetadata, $removeMethod));
	}

}
