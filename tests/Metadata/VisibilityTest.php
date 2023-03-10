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

	public function testLooserOrEqualVisibilityPublic(): void
	{
		$public = Visibility::get(Visibility::VISIBILITY_PUBLIC);
		Assert::assertTrue($public->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PUBLIC)));
		Assert::assertTrue($public->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PROTECTED)));
		Assert::assertTrue($public->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PRIVATE)));
	}

	public function testLooserOrEqualVisibilityProtected(): void
	{
		$protected = Visibility::get(Visibility::VISIBILITY_PROTECTED);
		Assert::assertFalse($protected->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PUBLIC)));
		Assert::assertTrue($protected->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PROTECTED)));
		Assert::assertTrue($protected->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PRIVATE)));
	}

	public function testLooserOrEqualVisibilityPrivate(): void
	{
		$private = Visibility::get(Visibility::VISIBILITY_PRIVATE);
		Assert::assertFalse($private->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PUBLIC)));
		Assert::assertFalse($private->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PROTECTED)));
		Assert::assertTrue($private->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PRIVATE)));
	}

	public function testGetRequiredVisibilitySameClass(): void
	{
		Assert::assertSame(
			Visibility::get(Visibility::VISIBILITY_PRIVATE),
			Visibility::getRequiredVisibility(FooClass::class, FooClass::class)
		);
	}

	public function testGetRequiredVisibilityClassExtends(): void
	{
		Assert::assertSame(
			Visibility::get(Visibility::VISIBILITY_PROTECTED),
			Visibility::getRequiredVisibility(FooClass::class, BarClass::class)
		);
	}

	public function testGetRequiredVisibilityClassExtended(): void
	{
		Assert::assertSame(
			Visibility::get(Visibility::VISIBILITY_PROTECTED),
			Visibility::getRequiredVisibility(BarClass::class, FooClass::class)
		);
	}

	public function testGetRequiredVisibilityNoRelationClasses(): void
	{
		Assert::assertSame(
			Visibility::get(Visibility::VISIBILITY_PUBLIC),
			Visibility::getRequiredVisibility(FooClass::class, BazClass::class)
		);
	}

}
