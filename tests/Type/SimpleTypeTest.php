<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryAccess;
use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\Metadata\Visibility;

class SimpleTypeTest extends \PHPUnit\Framework\TestCase
{

	public function testProcessGetAndSet()
	{
		$integerSentry = new SimpleType();
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
		$propertyMetadata = new PropertyMetadata(
			'fooProperty',
			FooClass::class,
			'integer',
			new SentryIdentificator('integer'),
			false,
			[
				$getMethod,
				$setMethod,
			],
			null
		);
		$foo = new FooClass();

		$this->assertNull($integerSentry->processMethod($propertyMetadata, $foo, $getMethod, []));
		$this->assertNull($integerSentry->processMethod($propertyMetadata, $foo, $setMethod, [123]));
		$this->assertSame(123, $integerSentry->processMethod($propertyMetadata, $foo, $getMethod, []));
	}

	public function testProcessSetNull()
	{
		$integerSentry = new SimpleType();
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
		$propertyMetadata = new PropertyMetadata(
			'fooProperty',
			FooClass::class,
			'integer',
			new SentryIdentificator('integer|null'),
			true,
			[
				$getMethod,
				$setMethod,
			],
			null
		);
		$foo = new FooClass();

		$this->assertNull($integerSentry->processMethod($propertyMetadata, $foo, $getMethod, []));
		$this->assertNull($integerSentry->processMethod($propertyMetadata, $foo, $setMethod, [123]));
		$this->assertSame(123, $integerSentry->processMethod($propertyMetadata, $foo, $getMethod, []));

		$this->assertNull($integerSentry->processMethod($propertyMetadata, $foo, $setMethod, [null]));
		$this->assertNull($integerSentry->processMethod($propertyMetadata, $foo, $getMethod, []));
	}

	public function testProcessSetWrongType()
	{
		$integerSentry = new SimpleType();
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'fooProperty',
			FooClass::class,
			'integer',
			new SentryIdentificator('integer'),
			false,
			[
				$setMethod,
			],
			null
		);
		$foo = new FooClass();

		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('integer expected, 123 [string] given');

		$integerSentry->processMethod($propertyMetadata, $foo, $setMethod, ['123']);
	}

	public function testProcessSetNullableWrongType()
	{
		$integerSentry = new SimpleType();
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'fooProperty',
			FooClass::class,
			'integer',
			new SentryIdentificator('integer|null'),
			true,
			[
				$setMethod,
			],
			null
		);
		$foo = new FooClass();

		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('integer|null expected, 123 [string] given');

		$integerSentry->processMethod($propertyMetadata, $foo, $setMethod, ['123']);
	}

	public function testGenerateGet()
	{
		$integerSentry = new SimpleType();
		$getMethod = new SentryMethod(
			new SentryAccess('get'),
			'getFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'fooProperty',
			FooClass::class,
			'integer',
			new SentryIdentificator('integer'),
			false,
			[
				$getMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated integer getter
	 *
	 * @return integer
	 */
	public function getFoo()
	{
		return $this->fooProperty;
	}';
		$this->assertSame($method, $integerSentry->generateMethod($propertyMetadata, $getMethod));
	}

	public function testGenerateNullableGet()
	{
		$integerSentry = new SimpleType();
		$getMethod = new SentryMethod(
			new SentryAccess('get'),
			'getFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'fooProperty',
			FooClass::class,
			'integer',
			new SentryIdentificator('integer|null'),
			true,
			[
				$getMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated integer getter
	 *
	 * @return integer|null
	 */
	public function getFoo()
	{
		return $this->fooProperty;
	}';
		$this->assertSame($method, $integerSentry->generateMethod($propertyMetadata, $getMethod));
	}

	public function testGenerateSet()
	{
		$integerSentry = new SimpleType();
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'fooProperty',
			FooClass::class,
			'integer',
			new SentryIdentificator('integer'),
			false,
			[
				$setMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated integer setter
	 *
	 * @param integer $newValue
	 */
	public function setFoo($newValue)
	{
		\Consistence\Type\Type::checkType($newValue, \'integer\');
		$this->fooProperty = $newValue;
	}';
		$this->assertSame($method, $integerSentry->generateMethod($propertyMetadata, $setMethod));
	}

	public function testGenerateSetNullable()
	{
		$integerSentry = new SimpleType();
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'fooProperty',
			FooClass::class,
			'integer',
			new SentryIdentificator('integer|null'),
			true,
			[
				$setMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated integer setter
	 *
	 * @param integer|null $newValue
	 */
	public function setFoo($newValue)
	{
		\Consistence\Type\Type::checkType($newValue, \'integer|null\');
		$this->fooProperty = $newValue;
	}';
		$this->assertSame($method, $integerSentry->generateMethod($propertyMetadata, $setMethod));
	}

}
