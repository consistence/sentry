<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

use Generator;
use PHPUnit\Framework\Assert;

class VisibilityTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @return mixed[][]|\Generator
	 */
	public function validVisibilityValueDataProvider(): Generator
	{
		yield 'private' => [
			'value' => Visibility::VISIBILITY_PRIVATE,
		];

		yield 'protected' => [
			'value' => Visibility::VISIBILITY_PROTECTED,
		];

		yield 'public' => [
			'value' => Visibility::VISIBILITY_PUBLIC,
		];
	}

	/**
	 * @dataProvider validVisibilityValueDataProvider
	 *
	 * @param mixed $value
	 */
	public function testCreate($value): void
	{
		Assert::assertInstanceOf(Visibility::class, Visibility::get($value));
	}

	public function testGetName(): void
	{
		Assert::assertSame('public', Visibility::get(Visibility::VISIBILITY_PUBLIC)->getName());
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function isLooserOrEqualToDataProvider(): Generator
	{
		yield 'public' => [
			'visibility' => Visibility::get(Visibility::VISIBILITY_PUBLIC),
			'expectedIsLooserOrEqualToPublic' => true,
			'expectedIsLooserOrEqualToProtected' => true,
			'expectedIsLooserOrEqualToPrivate' => true,
		];

		yield 'protected' => [
			'visibility' => Visibility::get(Visibility::VISIBILITY_PROTECTED),
			'expectedIsLooserOrEqualToPublic' => false,
			'expectedIsLooserOrEqualToProtected' => true,
			'expectedIsLooserOrEqualToPrivate' => true,
		];

		yield 'private' => [
			'visibility' => Visibility::get(Visibility::VISIBILITY_PRIVATE),
			'expectedIsLooserOrEqualToPublic' => false,
			'expectedIsLooserOrEqualToProtected' => false,
			'expectedIsLooserOrEqualToPrivate' => true,
		];
	}

	/**
	 * @dataProvider isLooserOrEqualToDataProvider
	 *
	 * @param \Consistence\Sentry\Metadata\Visibility $visibility
	 * @param bool $expectedIsLooserOrEqualToPublic
	 * @param bool $expectedIsLooserOrEqualToProtected
	 * @param bool $expectedIsLooserOrEqualToPrivate
	 */
	public function testIsLooserOrEqualTo(
		Visibility $visibility,
		bool $expectedIsLooserOrEqualToPublic,
		bool $expectedIsLooserOrEqualToProtected,
		bool $expectedIsLooserOrEqualToPrivate
	): void
	{
		Assert::assertSame($expectedIsLooserOrEqualToPublic, $visibility->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PUBLIC)));
		Assert::assertSame($expectedIsLooserOrEqualToProtected, $visibility->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PROTECTED)));
		Assert::assertSame($expectedIsLooserOrEqualToPrivate, $visibility->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PRIVATE)));
	}

	/**
	 * @return mixed[][]|\Generator
	 */
	public function getRequiredVisibilityDataProvider(): Generator
	{
		yield 'same class' => [
			'targetClassFqn' => FooClass::class,
			'originClassFqn' => FooClass::class,
			'expectedRequiredVisibility' => Visibility::get(Visibility::VISIBILITY_PRIVATE),
		];

		yield 'class extends' => [
			'targetClassFqn' => FooClass::class,
			'originClassFqn' => BarClass::class,
			'expectedRequiredVisibility' => Visibility::get(Visibility::VISIBILITY_PROTECTED),
		];

		yield 'class extended' => [
			'targetClassFqn' => BarClass::class,
			'originClassFqn' => FooClass::class,
			'expectedRequiredVisibility' => Visibility::get(Visibility::VISIBILITY_PROTECTED),
		];

		yield 'no relation classes' => [
			'targetClassFqn' => FooClass::class,
			'originClassFqn' => BazClass::class,
			'expectedRequiredVisibility' => Visibility::get(Visibility::VISIBILITY_PUBLIC),
		];
	}

	/**
	 * @dataProvider getRequiredVisibilityDataProvider
	 *
	 * @param string $targetClassFqn
	 * @param string $originClassFqn
	 * @param \Consistence\Sentry\Metadata\Visibility $expectedRequiredVisibility
	 */
	public function testGetRequiredVisibility(
		string $targetClassFqn,
		string $originClassFqn,
		Visibility $expectedRequiredVisibility
	): void
	{
		Assert::assertSame($expectedRequiredVisibility, Visibility::getRequiredVisibility($targetClassFqn, $originClassFqn));
	}

}
