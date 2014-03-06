<?php

namespace Consistence\Sentry\Metadata;

class PropertyMetadataTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate()
	{
		$targetClass = 'BarClass';
		$sentryIdentificator = new SentryIdentificator($targetClass);
		$sentryMethods = [
			new SentryMethod(
				new SentryAccess('get'),
				'fooMethod',
				Visibility::get(Visibility::VISIBILITY_PUBLIC)
			),
		];
		$bidirectionalAssociation = new BidirectionalAssociation(
			$targetClass,
			'barProperty',
			BidirectionalAssociationType::get(BidirectionalAssociationType::ONE),
			[
				new SentryMethod(
					new SentryAccess('get'),
					'barMethod',
					Visibility::get(Visibility::VISIBILITY_PUBLIC)
				),
			]
		);
		$property = new PropertyMetadata(
			'fooProperty',
			'FooClass',
			$targetClass,
			$sentryIdentificator,
			false,
			$sentryMethods,
			$bidirectionalAssociation
		);

		$this->assertSame('fooProperty', $property->getName());
		$this->assertSame('FooClass', $property->getClassName());
		$this->assertSame($targetClass, $property->getType());
		$this->assertSame($sentryIdentificator, $property->getSentryIdentificator());
		$this->assertSame(false, $property->isNullable());
		$this->assertSame($sentryMethods, $property->getSentryMethods());
		$this->assertSame($bidirectionalAssociation, $property->getBidirectionalAssociation());
	}

	public function testCreateScalar()
	{
		$sentryIdentificator = new SentryIdentificator('integer');
		$property = new PropertyMetadata(
			'fooProperty',
			'FooClass',
			'integer',
			$sentryIdentificator,
			false,
			[],
			null
		);

		$this->assertSame('fooProperty', $property->getName());
		$this->assertSame('FooClass', $property->getClassName());
		$this->assertSame('integer', $property->getType());
		$this->assertSame($sentryIdentificator, $property->getSentryIdentificator());
		$this->assertSame(false, $property->isNullable());
		$this->assertEmpty($property->getSentryMethods());
		$this->assertNull($property->getBidirectionalAssociation());
	}

}
