<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

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

	public function testGetTargetMethodForType(): void
	{
		$sentryMethods = [
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
		];
		$bidirectionalAssociation = new BidirectionalAssociation(
			'FooClass',
			'fooProperty',
			BidirectionalAssociationType::get(BidirectionalAssociationType::ONE),
			$sentryMethods
		);

		$targetMethod = $bidirectionalAssociation->getTargetMethodForType(
			new SentryAccess('set'),
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		Assert::assertSame('setFoo', $targetMethod->getMethodName());
		Assert::assertTrue($targetMethod->getMethodVisibility()->equalsValue(Visibility::VISIBILITY_PUBLIC));
		Assert::assertTrue($targetMethod->getSentryAccess()->equals(new SentryAccess('set')));
	}

	public function testGetTargetMethodForTypeLooserVisibility(): void
	{
		$sentryMethods = [
			new SentryMethod(
				new SentryAccess('set'),
				'setFoo',
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			),
		];
		$bidirectionalAssociation = new BidirectionalAssociation(
			'FooClass',
			'fooProperty',
			BidirectionalAssociationType::get(BidirectionalAssociationType::ONE),
			$sentryMethods
		);

		$targetMethod = $bidirectionalAssociation->getTargetMethodForType(
			new SentryAccess('set'),
			Visibility::get(Visibility::VISIBILITY_PRIVATE)
		);
		Assert::assertSame('setFoo', $targetMethod->getMethodName());
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

	public function testGetTargetMethodForTypePickByVisibility(): void
	{
		$sentryMethods = [
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
		];
		$bidirectionalAssociation = new BidirectionalAssociation(
			'FooClass',
			'fooProperty',
			BidirectionalAssociationType::get(BidirectionalAssociationType::ONE),
			$sentryMethods
		);

		$targetMethod = $bidirectionalAssociation->getTargetMethodForType(
			new SentryAccess('set'),
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		Assert::assertSame('setPublic', $targetMethod->getMethodName());
		Assert::assertTrue($targetMethod->getMethodVisibility()->equalsValue(Visibility::VISIBILITY_PUBLIC));
		Assert::assertTrue($targetMethod->getSentryAccess()->equals(new SentryAccess('set')));
	}

	public function testGetTargetMethodForTypeMultipleSentryAccessPickByOrder(): void
	{
		$sentryMethods = [
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
		];
		$bidirectionalAssociation = new BidirectionalAssociation(
			'FooClass',
			'fooProperty',
			BidirectionalAssociationType::get(BidirectionalAssociationType::ONE),
			$sentryMethods
		);

		$targetMethod = $bidirectionalAssociation->getTargetMethodForType(
			new SentryAccess('set'),
			Visibility::get(Visibility::VISIBILITY_PUBLIC)
		);
		Assert::assertSame('setFirst', $targetMethod->getMethodName());
		Assert::assertTrue($targetMethod->getMethodVisibility()->equalsValue(Visibility::VISIBILITY_PUBLIC));
		Assert::assertTrue($targetMethod->getSentryAccess()->equals(new SentryAccess('set')));
	}

}
