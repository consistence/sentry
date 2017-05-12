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
			'int',
			new SentryIdentificator('int'),
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
			'int',
			new SentryIdentificator('int|null'),
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
			'int',
			new SentryIdentificator('int'),
			false,
			[
				$setMethod,
			],
			null
		);
		$foo = new FooClass();

		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('int expected, 123 [string] given');

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
			'int',
			new SentryIdentificator('int|null'),
			true,
			[
				$setMethod,
			],
			null
		);
		$foo = new FooClass();

		$this->expectException(\Consistence\InvalidArgumentTypeException::class);
		$this->expectExceptionMessage('int|null expected, 123 [string] given');

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
			'int',
			new SentryIdentificator('int'),
			false,
			[
				$getMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated int getter
	 *
	 * @return int
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
			'int',
			new SentryIdentificator('int|null'),
			true,
			[
				$getMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated int getter
	 *
	 * @return int|null
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
			'int',
			new SentryIdentificator('int'),
			false,
			[
				$setMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated int setter
	 *
	 * @param int $newValue
	 */
	public function setFoo($newValue)
	{
		\Consistence\Type\Type::checkType($newValue, \'int\');
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
			'int',
			new SentryIdentificator('int|null'),
			true,
			[
				$setMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated int setter
	 *
	 * @param int|null $newValue
	 */
	public function setFoo($newValue)
	{
		\Consistence\Type\Type::checkType($newValue, \'int|null\');
		$this->fooProperty = $newValue;
	}';
		$this->assertSame($method, $integerSentry->generateMethod($propertyMetadata, $setMethod));
	}

}
