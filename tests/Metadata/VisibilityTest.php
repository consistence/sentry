<?php

namespace Consistence\Sentry\Metadata;

class VisibilityTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate()
	{
		$this->assertInstanceOf(Visibility::class, Visibility::get(Visibility::VISIBILITY_PRIVATE));
		$this->assertInstanceOf(Visibility::class, Visibility::get(Visibility::VISIBILITY_PROTECTED));
		$this->assertInstanceOf(Visibility::class, Visibility::get(Visibility::VISIBILITY_PUBLIC));
	}

}
