<?php

namespace Consistence\Sentry\Runtime;

use Consistence\Sentry\Factory\SentryFactory;
use Consistence\Sentry\Metadata\ClassMetadata;
use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\Metadata\SentryMethodSearchResult;
use Consistence\Sentry\Metadata\Visibility;
use Consistence\Sentry\MetadataSource\MetadataSource;
use Consistence\Sentry\Type\Sentry;

use ReflectionClass;

class RuntimeHelperTest extends \PHPUnit\Framework\TestCase
{

	public function testRun()
	{
		$foo = new FooClass();
		$fooMethod = 'fooMethod';
		$fooParam = 'test';

		$sentryIdentificator = $this->createMock(SentryIdentificator::class);

		$property = $this->createMock(PropertyMetadata::class);
		$property
			->expects($this->once())
			->method('getSentryIdentificator')
			->will($this->returnValue($sentryIdentificator));

		$sentryMethod = $this->createMock(SentryMethod::class);

		$searchResult = new SentryMethodSearchResult($sentryMethod, $property);

		$classMetadata = $this->createMock(ClassMetadata::class);
		$classMetadata
			->expects($this->once())
			->method('getSentryMethodByNameAndRequiredVisibility')
			->with($fooMethod, $this->identicalTo(Visibility::get(Visibility::VISIBILITY_PRIVATE)))
			->will($this->returnValue($searchResult));

		$metadataSource = $this->createMock(MetadataSource::class);
		$metadataSource
			->expects($this->once())
			->method('getMetadataForClass')
			->with(new ReflectionClass($foo))
			->will($this->returnValue($classMetadata));

		$sentry = $this->createMock(Sentry::class);
		$sentry
			->expects($this->once())
			->method('processMethod')
			->with(
				$this->identicalTo($property),
				$this->identicalTo($foo),
				$this->identicalTo($sentryMethod),
				[$fooParam]
			);

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects($this->once())
			->method('getSentry')
			->with($this->identicalTo($sentryIdentificator))
			->will($this->returnValue($sentry));

		$runtimeHelper = new RuntimeHelper($metadataSource, $sentryFactory);

		$runtimeHelper->run($foo, $fooMethod, [$fooParam]);
	}

	public function testMethodNotFoundCallbackParentDoesNotImplementSentryAware()
	{
		$foo = new FooClass();
		$fooMethod = 'fooMethod';
		$fooParam = 'test';

		$classMetadata = $this->createMock(ClassMetadata::class);
		$classMetadata
			->expects($this->once())
			->method('getSentryMethodByNameAndRequiredVisibility')
			->with($fooMethod, $this->identicalTo(Visibility::get(Visibility::VISIBILITY_PRIVATE)))
			->will($this->throwException(
				new \Consistence\Sentry\Metadata\MethodNotFoundException($fooMethod, get_class($foo))
			));

		$metadataSource = $this->createMock(MetadataSource::class);
		$metadataSource
			->expects($this->once())
			->method('getMetadataForClass')
			->with(new ReflectionClass($foo))
			->will($this->returnValue($classMetadata));

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects($this->never())
			->method('getSentry');

		$runtimeHelper = new RuntimeHelper($metadataSource, $sentryFactory);

		$this->assertSame('test', $runtimeHelper->run($foo, $fooMethod, [$fooParam], function () {
			return 'test';
		}));
	}

	public function testMethodNotFoundCallbackParentImplementsSentryAware()
	{
		$foo = new BarClass();
		$fooMethod = 'fooMethod';
		$fooParam = 'test';

		$metadataReturnCallback = function () use ($fooMethod, $foo) {
			$classMetadata = $this->createMock(ClassMetadata::class);
			$classMetadata
				->expects($this->once())
				->method('getSentryMethodByNameAndRequiredVisibility')
				->with($fooMethod, $this->identicalTo(Visibility::get(Visibility::VISIBILITY_PRIVATE)))
				->will($this->throwException(
					new \Consistence\Sentry\Metadata\MethodNotFoundException($fooMethod, get_class($foo))
				));

			return $classMetadata;
		};

		$metadataSource = $this->createMock(MetadataSource::class);
		$metadataSource
			->expects($this->exactly(2))
			->method('getMetadataForClass')
			->will($this->returnCallback($metadataReturnCallback));

		$sentryFactory = $this->createMock(SentryFactory::class);
		$sentryFactory
			->expects($this->never())
			->method('getSentry');

		$runtimeHelper = new RuntimeHelper($metadataSource, $sentryFactory);

		$this->assertSame('test', $runtimeHelper->run($foo, $fooMethod, [$fooParam], function () {
			return 'test';
		}));
	}

}
