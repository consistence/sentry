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
