<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\BidirectionalAssociationType;
use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryAccess;
use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\Metadata\Visibility;
use Generator;
use PHPUnit\Framework\Assert;

class AbstractSentryTest extends \PHPUnit\Framework\TestCase
{

	public function testSupportedAccess(): void
	{
		$sentry = $this->getMockForAbstractClass(AbstractSentry::class);

		Assert::assertEquals([
			new SentryAccess('get'),
			new SentryAccess('set'),
		], $sentry->getSupportedAccess());
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function getDefaultMethodNameDataProvider(): Generator
	{
		yield 'get' => [
			'sentryAccess' => new SentryAccess('get'),
			'propertyName' => 'foo',
			'expectedDefaultMethodName' => 'getFoo',
		];

		yield 'set' => [
			'sentryAccess' => new SentryAccess('set'),
			'propertyName' => 'foo',
			'expectedDefaultMethodName' => 'setFoo',
		];
	}

	/**
	 * @dataProvider getDefaultMethodNameDataProvider
	 *
	 * @param \Consistence\Sentry\Metadata\SentryAccess $sentryAccess
	 * @param string $propertyName
	 * @param string $expectedDefaultMethodName
	 */
	public function testGetDefaultMethodName(
		SentryAccess $sentryAccess,
		string $propertyName,
		string $expectedDefaultMethodName
	): void
	{
		$sentry = $this->getMockForAbstractClass(AbstractSentry::class);

		Assert::assertSame($expectedDefaultMethodName, $sentry->getDefaultMethodName($sentryAccess, $propertyName));
	}

	public function testDefaultMethodNameUnsupportedSentryAccess(): void
	{
		$sentry = $this->getMockForAbstractClass(AbstractSentry::class, [], 'MockAbstractSentryTest');

		$sentryAccess = new SentryAccess('xxx');
		try {
			$sentry->getDefaultMethodName($sentryAccess, 'foo');
			Assert::fail('Exception expected');
		} catch (\Consistence\Sentry\Type\SentryAccessNotSupportedException $e) {
			Assert::assertSame($sentryAccess, $e->getSentryAccess());
			Assert::assertSame('MockAbstractSentryTest', $e->getSentryClassName());
		}
	}

	public function testTargetAssociationAccessForAccess(): void
	{
		$sentry = $this->getMockForAbstractClass(AbstractSentry::class);

		Assert::assertEmpty($sentry->getTargetAssociationAccessForAccess(
			new SentryAccess('get'),
			BidirectionalAssociationType::get(BidirectionalAssociationType::ONE)
		));
	}

	public function testGenerateGet(): void
	{
		$sentry = $this->getMockForAbstractClass(AbstractSentry::class);
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
		Assert::assertSame($method, $sentry->generateMethod($propertyMetadata, $getMethod));
	}

	public function testGenerateObjectGet(): void
	{
		$sentry = $this->getMockForAbstractClass(AbstractSentry::class);
		$getMethod = new SentryMethod(
			new SentryAccess('get'),
			'getFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'fooProperty',
			FooClass::class,
			'stdClass',
			new SentryIdentificator('stdClass'),
			false,
			[
				$getMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated stdClass getter
	 *
	 * @return \stdClass
	 */
	public function getFoo()
	{
		return $this->fooProperty;
	}';
		Assert::assertSame($method, $sentry->generateMethod($propertyMetadata, $getMethod));
	}

	public function testGenerateSet(): void
	{
		$sentry = $this->getMockForAbstractClass(AbstractSentry::class);
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
		$this->fooProperty = $newValue;
	}';
		Assert::assertSame($method, $sentry->generateMethod($propertyMetadata, $setMethod));
	}

	public function testGenerateObjectSet(): void
	{
		$sentry = $this->getMockForAbstractClass(AbstractSentry::class);
		$setMethod = new SentryMethod(
			new SentryAccess('set'),
			'setFoo',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'fooProperty',
			FooClass::class,
			'stdClass',
			new SentryIdentificator('stdClass'),
			false,
			[
				$setMethod,
			],
			null
		);

		$method = '
	/**
	 * Generated stdClass setter
	 *
	 * @param \stdClass $newValue
	 */
	public function setFoo($newValue)
	{
		$this->fooProperty = $newValue;
	}';
		Assert::assertSame($method, $sentry->generateMethod($propertyMetadata, $setMethod));
	}

	public function testGenerateUnsupportedSentryAccess(): void
	{
		$sentry = $this->getMockForAbstractClass(AbstractSentry::class);
		$xxxSentryAccess = new SentryAccess('xxx');
		$xxxMethod = new SentryMethod(
			$xxxSentryAccess,
			'xxxMethod',
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		$propertyMetadata = new PropertyMetadata(
			'fooProperty',
			FooClass::class,
			'int',
			new SentryIdentificator('int'),
			false,
			[
				$xxxMethod,
			],
			null
		);

		$args = [];
		try {
			$sentry->generateMethod($propertyMetadata, $xxxMethod, $args);
			Assert::fail('Exception expected');
		} catch (\Consistence\Sentry\Type\SentryAccessNotSupportedForPropertyException $e) {
			Assert::assertSame($propertyMetadata, $e->getProperty());
			Assert::assertSame($xxxSentryAccess, $e->getSentryAccess());
			Assert::assertSame(get_class($sentry), $e->getSentryClassName());
		}
	}

}
