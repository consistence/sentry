<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

use Generator;
use PHPUnit\Framework\Assert;

class BidirectionalAssociationTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate(): void
	{
		$sentryMethods = [
			new SentryMethod(
				new SentryAccess('get'),
				'getFoo',
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			),
		];
		$bidirectionalAssociation = new BidirectionalAssociation(
			'FooClass',
			'fooProperty',
			BidirectionalAssociationType::get(BidirectionalAssociationType::ONE),
			$sentryMethods
		);

		Assert::assertSame('FooClass', $bidirectionalAssociation->getTargetClass());
		Assert::assertSame('fooProperty', $bidirectionalAssociation->getTargetProperty());
		Assert::assertTrue($bidirectionalAssociation->getTargetType()->equalsValue(BidirectionalAssociationType::ONE));
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function getTargetMethodForTypeDataProvider(): Generator
	{
		yield 'two public methods with different SentryAccess type' => [
			'sentryMethods' => [
				new SentryMethod(
					new SentryAccess('get'),
					'getFoo',
					Visibility::get(Visibility::VISIBILITY_PUBLIC)
				),
				new SentryMethod(
					new SentryAccess('set'),
					'setFoo',
					Visibility::get(Visibility::VISIBILITY_PUBLIC)
				),
			],
			'visibility' => Visibility::get(Visibility::VISIBILITY_PUBLIC),
			'expectedMethodName' => 'setFoo',
		];

		yield 'single public method, looser visibility' => [
			'sentryMethods' => [
				new SentryMethod(
					new SentryAccess('set'),
					'setFoo',
					Visibility::get(Visibility::VISIBILITY_PUBLIC)
				),
			],
			'visibility' => Visibility::get(Visibility::VISIBILITY_PRIVATE),
			'expectedMethodName' => 'setFoo',
		];

		yield 'one public and one private method with same SentryAccess type, pick by visibility' => [
			'sentryMethods' => [
				new SentryMethod(
					new SentryAccess('set'),
					'setPrivate',
					Visibility::get(Visibility::VISIBILITY_PRIVATE)
				),
				new SentryMethod(
					new SentryAccess('set'),
					'setPublic',
					Visibility::get(Visibility::VISIBILITY_PUBLIC)
				),
			],
			'visibility' => Visibility::get(Visibility::VISIBILITY_PUBLIC),
			'expectedMethodName' => 'setPublic',
		];

		yield 'two public methods with same SentryAccess type, pick by order' => [
			'sentryMethods' => [
				new SentryMethod(
					new SentryAccess('set'),
					'setFirst',
					Visibility::get(Visibility::VISIBILITY_PUBLIC)
				),
				new SentryMethod(
					new SentryAccess('set'),
					'setSecond',
					Visibility::get(Visibility::VISIBILITY_PUBLIC)
				),
			],
			'visibility' => Visibility::get(Visibility::VISIBILITY_PUBLIC),
			'expectedMethodName' => 'setFirst',
		];
	}

	/**
	 * @dataProvider getTargetMethodForTypeDataProvider
	 *
	 * @param \Consistence\Sentry\Metadata\SentryMethod[] $sentryMethods
	 * @param \Consistence\Sentry\Metadata\Visibility $visibility
	 * @param string $expectedMethodName
	 */
	public function testGetTargetMethodForType(
		array $sentryMethods,
		Visibility $visibility,
		string $expectedMethodName
	): void
	{
		$bidirectionalAssociation = new BidirectionalAssociation(
			'FooClass',
			'fooProperty',
			BidirectionalAssociationType::get(BidirectionalAssociationType::ONE),
			$sentryMethods
		);

		$targetMethod = $bidirectionalAssociation->getTargetMethodForType(
			new SentryAccess('set'),
			$visibility
		);
		Assert::assertSame($expectedMethodName, $targetMethod->getMethodName());
		Assert::assertTrue($targetMethod->getMethodVisibility()->equalsValue(Visibility::VISIBILITY_PUBLIC));
		Assert::assertTrue($targetMethod->getSentryAccess()->equals(new SentryAccess('set')));
	}

	public function testGetTargetMethodForTypeRequiredVisibilityNotFound(): void
	{
		$sentryMethods = [
			new SentryMethod(
				new SentryAccess('set'),
				'setFoo',
				Visibility::get(Visibility::VISIBILITY_PRIVATE)
			),
		];
		$bidirectionalAssociation = new BidirectionalAssociation(
			'FooClass',
			'fooProperty',
			BidirectionalAssociationType::get(BidirectionalAssociationType::ONE),
			$sentryMethods
		);

		try {
			$bidirectionalAssociation->getTargetMethodForType(
				new SentryAccess('set'),
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			);
			Assert::fail('Exception expected');
		} catch (\Consistence\Sentry\Metadata\NoSuitableMethodException $e) {
			Assert::assertSame('FooClass', $e->getClassName());
			Assert::assertSame('fooProperty', $e->getPropertyName());
			Assert::assertTrue($e->getSentryAccess()->equals(new SentryAccess('set')));
		}
	}

}
