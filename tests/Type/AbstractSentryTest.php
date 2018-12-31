<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\BidirectionalAssociationType;
use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryAccess;
use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\Metadata\Visibility;

class AbstractSentryTest extends \PHPUnit\Framework\TestCase
{

	public function testSupportedAccess()
	{
		$sentry = $this->getMockForAbstractClass(AbstractSentry::class);

		$this->assertEquals([
			new SentryAccess('get'),
			new SentryAccess('set'),
		], $sentry->getSupportedAccess());
	}

	public function testDefaultMethodNames()
	{
		$sentry = $this->getMockForAbstractClass(AbstractSentry::class);

		$this->assertSame('getFoo', $sentry->getDefaultMethodName(new SentryAccess('get'), 'foo'));
		$this->assertSame('setFoo', $sentry->getDefaultMethodName(new SentryAccess('set'), 'foo'));
	}

	public function testDefaultMethodNameUnsupportedSentryAccess()
	{
		$sentry = $this->getMockForAbstractClass(AbstractSentry::class, [], 'MockAbstractSentryTest');

		$sentryAccess = new SentryAccess('xxx');
		try {
			$sentry->getDefaultMethodName($sentryAccess, 'foo');
			$this->fail();
		} catch (\Consistence\Sentry\Type\SentryAccessNotSupportedException $e) {
			$this->assertSame($sentryAccess, $e->getSentryAccess());
			$this->assertSame('MockAbstractSentryTest', $e->getSentryClassName());
		}
	}

	public function testTargetAssociationAccessForAccess()
	{
		$sentry = $this->getMockForAbstractClass(AbstractSentry::class);

		$this->assertEmpty($sentry->getTargetAssociationAccessForAccess(
			new SentryAccess('get'),
			BidirectionalAssociationType::get(BidirectionalAssociationType::ONE)
		));
	}

	public function testGenerateGet()
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
		$this->assertSame($method, $sentry->generateMethod($propertyMetadata, $getMethod));
	}


	public function testGenerateObjectGet()
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
		$this->assertSame($method, $sentry->generateMethod($propertyMetadata, $getMethod));
	}

	public function testGenerateSet()
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
		$this->assertSame($method, $sentry->generateMethod($propertyMetadata, $setMethod));
	}

	public function testGenerateObjectSet()
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
		$this->assertSame($method, $sentry->generateMethod($propertyMetadata, $setMethod));
	}

	public function testGenerateUnsupportedSentryAccess()
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
			$this->fail();
		} catch (\Consistence\Sentry\Type\SentryAccessNotSupportedForPropertyException $e) {
			$this->assertSame($propertyMetadata, $e->getProperty());
			$this->assertSame($xxxSentryAccess, $e->getSentryAccess());
			$this->assertSame(get_class($sentry), $e->getSentryClassName());
		}
	}

}
