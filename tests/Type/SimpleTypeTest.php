<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryAccess;
use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\Metadata\Visibility;
use Generator;
use PHPUnit\Framework\Assert;

class SimpleTypeTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function generateMethodDataProvider(): Generator
	{
		yield 'get' => (function (): array {
			$sentryMethod = new SentryMethod(
				new SentryAccess('get'),
				'getFoo',
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			);

			return [
				'propertyMetadata' => new PropertyMetadata(
					'fooProperty',
					FooClass::class,
					'int',
					new SentryIdentificator('int'),
					false,
					[
						$sentryMethod,
					],
					null
				),
				'sentryMethod' => $sentryMethod,
				'expectedGeneratedMethod' => '
	/**
	 * Generated int getter
	 *
	 * @return int
	 */
	public function getFoo()
	{
		return $this->fooProperty;
	}',
			];
		})();

		yield 'get nullable' => (function (): array {
			$sentryMethod = new SentryMethod(
				new SentryAccess('get'),
				'getFoo',
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			);

			return [
				'propertyMetadata' => new PropertyMetadata(
					'fooProperty',
					FooClass::class,
					'int',
					new SentryIdentificator('int|null'),
					true,
					[
						$sentryMethod,
					],
					null
				),
				'sentryMethod' => $sentryMethod,
				'expectedGeneratedMethod' => '
	/**
	 * Generated int getter
	 *
	 * @return int|null
	 */
	public function getFoo()
	{
		return $this->fooProperty;
	}',
			];
		})();

		yield 'set' => (function (): array {
			$sentryMethod = new SentryMethod(
				new SentryAccess('set'),
				'setFoo',
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			);

			return [
				'propertyMetadata' => new PropertyMetadata(
					'fooProperty',
					FooClass::class,
					'int',
					new SentryIdentificator('int'),
					false,
					[
						$sentryMethod,
					],
					null
				),
				'sentryMethod' => $sentryMethod,
				'expectedGeneratedMethod' => '
	/**
	 * Generated int setter
	 *
	 * @param int $newValue
	 */
	public function setFoo($newValue)
	{
		\Consistence\Type\Type::checkType($newValue, \'int\');
		$this->fooProperty = $newValue;
	}',
			];
		})();

		yield 'set nullable' => (function (): array {
			$sentryMethod = new SentryMethod(
				new SentryAccess('set'),
				'setFoo',
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			);

			return [
				'propertyMetadata' => new PropertyMetadata(
					'fooProperty',
					FooClass::class,
					'int',
					new SentryIdentificator('int|null'),
					true,
					[
						$sentryMethod,
					],
					null
				),
				'sentryMethod' => $sentryMethod,
				'expectedGeneratedMethod' => '
	/**
	 * Generated int setter
	 *
	 * @param int|null $newValue
	 */
	public function setFoo($newValue)
	{
		\Consistence\Type\Type::checkType($newValue, \'int|null\');
		$this->fooProperty = $newValue;
	}',
			];
		})();
	}

	/**
	 * @dataProvider generateMethodDataProvider
	 *
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $propertyMetadata
	 * @param \Consistence\Sentry\Metadata\SentryMethod $sentryMethod
	 * @param string $expectedGeneratedMethod
	 */
	public function testGenerateMethod(
		PropertyMetadata $propertyMetadata,
		SentryMethod $sentryMethod,
		string $expectedGeneratedMethod
	): void
	{
		$integerSentry = new SimpleType();

		Assert::assertSame($expectedGeneratedMethod, $integerSentry->generateMethod($propertyMetadata, $sentryMethod));
	}

}
