<?php

namespace Consistence\Sentry\Metadata;

class ClassMetadataTest extends \PHPUnit\Framework\TestCase
{

	public function testCreate()
	{
		$properties = [
			new PropertyMetadata(
				'fooProperty',
				'FooClass',
				'integer',
				new SentryIdentificator('integer'),
				false,
				[],
				null
			)
		];

		$classMetadata = new ClassMetadata(
			'FooClass',
			$properties
		);
		$this->assertSame('FooClass', $classMetadata->getName());
		$this->assertSame($properties, $classMetadata->getProperties());
	}

}
