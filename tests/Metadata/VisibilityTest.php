<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

class VisibilityTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate()
	{
		$this->assertInstanceOf(Visibility::class, Visibility::get(Visibility::VISIBILITY_PRIVATE));
		$this->assertInstanceOf(Visibility::class, Visibility::get(Visibility::VISIBILITY_PROTECTED));
		$this->assertInstanceOf(Visibility::class, Visibility::get(Visibility::VISIBILITY_PUBLIC));
	}

	public function testGetName()
	{
		$this->assertSame('public', Visibility::get(Visibility::VISIBILITY_PUBLIC)->getName());
	}

	public function testLooserOrEqualVisibilityPublic()
	{
		$public = Visibility::get(Visibility::VISIBILITY_PUBLIC);
		$this->assertTrue($public->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PUBLIC)));
		$this->assertTrue($public->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PROTECTED)));
		$this->assertTrue($public->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PRIVATE)));
	}

	public function testLooserOrEqualVisibilityProtected()
	{
		$protected = Visibility::get(Visibility::VISIBILITY_PROTECTED);
		$this->assertFalse($protected->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PUBLIC)));
		$this->assertTrue($protected->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PROTECTED)));
		$this->assertTrue($protected->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PRIVATE)));
	}

	public function testLooserOrEqualVisibilityPrivate()
	{
		$private = Visibility::get(Visibility::VISIBILITY_PRIVATE);
		$this->assertFalse($private->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PUBLIC)));
		$this->assertFalse($private->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PROTECTED)));
		$this->assertTrue($private->isLooserOrEqualTo(Visibility::get(Visibility::VISIBILITY_PRIVATE)));
	}

	public function testGetRequiredVisibilitySameClass()
	{
		$this->assertEquals(
			Visibility::get(Visibility::VISIBILITY_PRIVATE),
			Visibility::getRequiredVisibility(FooClass::class, FooClass::class)
		);
	}

	public function testGetRequiredVisibilityClassExtends()
	{
		$this->assertEquals(
			Visibility::get(Visibility::VISIBILITY_PROTECTED),
			Visibility::getRequiredVisibility(FooClass::class, BarClass::class)
		);
	}

	public function testGetRequiredVisibilityClassExtended()
	{
		$this->assertEquals(
			Visibility::get(Visibility::VISIBILITY_PROTECTED),
			Visibility::getRequiredVisibility(BarClass::class, FooClass::class)
		);
	}

	public function testGetRequiredVisibilityNoRelationClasses()
	{
		$this->assertEquals(
			Visibility::get(Visibility::VISIBILITY_PUBLIC),
			Visibility::getRequiredVisibility(FooClass::class, BazClass::class)
		);
	}

}
