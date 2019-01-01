<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

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

		$this->assertSame('FooClass', $bidirectionalAssociation->getTargetClass());
		$this->assertSame('fooProperty', $bidirectionalAssociation->getTargetProperty());
		$this->assertTrue($bidirectionalAssociation->getTargetType()->equalsValue(BidirectionalAssociationType::ONE));
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
		$this->assertSame('setFoo', $targetMethod->getMethodName());
		$this->assertTrue($targetMethod->getMethodVisibility()->equalsValue(Visibility::VISIBILITY_PUBLIC));
		$this->assertTrue($targetMethod->getSentryAccess()->equals(new SentryAccess('set')));
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
		$this->assertSame('setFoo', $targetMethod->getMethodName());
		$this->assertTrue($targetMethod->getMethodVisibility()->equalsValue(Visibility::VISIBILITY_PUBLIC));
		$this->assertTrue($targetMethod->getSentryAccess()->equals(new SentryAccess('set')));
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
			$this->fail();
		} catch (\Consistence\Sentry\Metadata\NoSuitableMethodException $e) {
			$this->assertSame('FooClass', $e->getClassName());
			$this->assertSame('fooProperty', $e->getPropertyName());
			$this->assertTrue($e->getSentryAccess()->equals(new SentryAccess('set')));
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
		$this->assertSame('setPublic', $targetMethod->getMethodName());
		$this->assertTrue($targetMethod->getMethodVisibility()->equalsValue(Visibility::VISIBILITY_PUBLIC));
		$this->assertTrue($targetMethod->getSentryAccess()->equals(new SentryAccess('set')));
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
		$this->assertSame('setFirst', $targetMethod->getMethodName());
		$this->assertTrue($targetMethod->getMethodVisibility()->equalsValue(Visibility::VISIBILITY_PUBLIC));
		$this->assertTrue($targetMethod->getSentryAccess()->equals(new SentryAccess('set')));
	}

}
