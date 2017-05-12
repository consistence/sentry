<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Runtime;

use Closure;

use Consistence\Sentry\Factory\SentryFactory;
use Consistence\Sentry\Metadata\Visibility;
use Consistence\Sentry\MetadataSource\MetadataSource;
use Consistence\Sentry\SentryAware;

use ReflectionClass;

class RuntimeHelper extends \Consistence\ObjectPrototype
{

	/** @var \Consistence\Sentry\MetadataSource\MetadataSource */
	private $metadataSource;

	/** @var \Consistence\Sentry\Factory\SentryFactory */
	private $sentryFactory;

	public function __construct(MetadataSource $metadataSource, SentryFactory $sentryFactory)
	{
		$this->metadataSource = $metadataSource;
		$this->sentryFactory = $sentryFactory;
	}

	/**
	 * @param \Consistence\Sentry\SentryAware $object
	 * @param string $methodName
	 * @param mixed[] $args
	 * @param \Closure|null $nothingDoneCallback
	 * @return mixed
	 */
	public function run(SentryAware $object, string $methodName, array $args, Closure $nothingDoneCallback = null)
	{
		$searchResult = $this->findMethod(new ReflectionClass($object), $methodName);
		if ($searchResult === null && $nothingDoneCallback !== null) {
			return $nothingDoneCallback($object, $methodName, $args);
		}
		$sentry = $this->sentryFactory->getSentry($searchResult->getProperty()->getSentryIdentificator());

		return $sentry->processMethod($searchResult->getProperty(), $object, $searchResult->getSentryMethod(), $args);
	}

	/**
	 * @param \ReflectionClass $classReflection
	 * @param string $methodName
	 * @return \Consistence\Sentry\Metadata\SentryMethodSearchResult|null
	 */
	private function findMethod(ReflectionClass $classReflection, string $methodName)
	{
		try {
			$classMetadata = $this->metadataSource->getMetadataForClass($classReflection);
			// in runtime, the context from which the method is called is not available, searches for all methods
			return $classMetadata->getSentryMethodByNameAndRequiredVisibility(
				$methodName,
				Visibility::get(Visibility::VISIBILITY_PRIVATE)
			);
		} catch (\Consistence\Sentry\Metadata\MethodNotFoundException $e) {
			return $this->findMethodInParentClass($classReflection, $methodName);
		}
	}

	/**
	 *
	 * @param \ReflectionClass $classReflection
	 * @param string $methodName
	 * @return \Consistence\Sentry\Metadata\SentryMethodSearchResult|null
	 */
	private function findMethodInParentClass(ReflectionClass $classReflection, string $methodName)
	{
		$parent = $classReflection->getParentClass();
		if ($parent !== false && $parent->implementsInterface(SentryAware::class) === true) {
			return $this->findMethod($parent, $methodName);
		}

		return null;
	}

}
