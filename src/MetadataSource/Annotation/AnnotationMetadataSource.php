<?php

namespace Consistence\Sentry\MetadataSource\Annotation;

use Consistence\Annotation\Annotation;
use Consistence\Annotation\AnnotationProvider;
use Consistence\Reflection\ClassReflection;
use Consistence\Sentry\Factory\SentryFactory;
use Consistence\Sentry\Metadata\ClassMetadata;
use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryAccess;
use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\Metadata\Visibility;
use Consistence\Sentry\SentryAware;
use Consistence\Sentry\SentryIdentificatorParser\SentryIdentificatorParser;
use Consistence\Sentry\Type\Sentry;

use ReflectionClass;
use ReflectionProperty;

class AnnotationMetadataSource extends \Consistence\ObjectPrototype implements \Consistence\Sentry\MetadataSource\MetadataSource
{

	const IDENTIFICATOR_ANNOTATION = 'var';

	const METHOD_PARAM_NAME = 'name';
	const METHOD_PARAM_VISIBILITY = 'visibility';

	const DEFAULT_VISIBILITY = Visibility::VISIBILITY_PUBLIC;

	/** @var \Consistence\Sentry\Factory\SentryFactory */
	private $sentryFactory;

	/** @var \Consistence\Sentry\SentryIdentificatorParser\SentryIdentificatorParser */
	private $sentryIdentificatorParser;

	/** @var \Consistence\Annotation\AnnotationProvider */
	private $annotationProvider;

	/** @var \Consistence\Sentry\Metadata\ClassMetadata[] */
	private $classMetadata;

	public function __construct(
		SentryFactory $sentryFactory,
		SentryIdentificatorParser $sentryIdentificatorParser,
		AnnotationProvider $annotationProvider
	)
	{
		$this->sentryFactory = $sentryFactory;
		$this->sentryIdentificatorParser = $sentryIdentificatorParser;
		$this->annotationProvider = $annotationProvider;
		$this->classMetadata = [];
	}

	/**
	 * @param \ReflectionClass $classReflection
	 * @return \Consistence\Sentry\Metadata\ClassMetadata
	 */
	public function getMetadataForClass(ReflectionClass $classReflection)
	{
		if ($classReflection->implementsInterface(SentryAware::class) === false) {
			throw new \Consistence\Sentry\MetadataSource\ClassMetadataCouldNotBeCreatedException(
				$classReflection,
				'Interface SentryAware is not implemented'
			);
		}
		if (!isset($this->classMetadata[$classReflection->getName()])) {
			$this->classMetadata[$classReflection->getName()] = $this->createClassMetadata($classReflection);
		}

		return $this->classMetadata[$classReflection->getName()];
	}

	/**
	 * @param \ReflectionClass $classReflection
	 * @return \Consistence\Sentry\Metadata\ClassMetadata
	 */
	private function createClassMetadata(ReflectionClass $classReflection)
	{
		$propertiesMetadata = [];
		foreach (ClassReflection::getDeclaredProperties($classReflection) as $propertyReflection) {
			try {
				$propertiesMetadata[] = $this->createPropertyMetadata($propertyReflection);
			} catch (\Consistence\Sentry\MetadataSource\Annotation\PropertyMetadataCouldNotBeCreatedException $e) {
				// skip
			}
		}

		return new ClassMetadata(
			$classReflection->getName(),
			$propertiesMetadata
		);
	}

	/**
	 * @param \ReflectionProperty $propertyReflection
	 * @return \Consistence\Sentry\Metadata\PropertyMetadata
	 */
	private function createPropertyMetadata(ReflectionProperty $propertyReflection)
	{
		try {
			$sentryIdentificator = $this->getSentryIdentificator($propertyReflection);
			try {
				$sentryIdentificatorParseResult = $this->sentryIdentificatorParser->parse($sentryIdentificator);
			} catch (\Consistence\Sentry\SentryIdentificatorParser\PatternDoesNotMatchException $e) {
				throw new \Consistence\Sentry\MetadataSource\Annotation\PropertyMetadataCouldNotBeCreatedException($e);
			}
			$sentry = $this->sentryFactory->getSentry($sentryIdentificator);
			$sentryMethods = $this->createSentryMethods($propertyReflection, $sentry);

			return new PropertyMetadata(
				$propertyReflection->getName(),
				$propertyReflection->getDeclaringClass()->getName(),
				$sentryIdentificatorParseResult->getType(),
				$sentryIdentificator,
				$sentryIdentificatorParseResult->isNullable(),
				$sentryMethods,
				null
			);
		} catch (\Consistence\Sentry\MetadataSource\Annotation\NoSentryIdentificatorException $e) {
			throw new \Consistence\Sentry\MetadataSource\Annotation\PropertyMetadataCouldNotBeCreatedException($e);
		} catch (\Consistence\Sentry\Factory\NoSentryForIdentificatorException $e) {
			throw new \Consistence\Sentry\MetadataSource\Annotation\PropertyMetadataCouldNotBeCreatedException($e);
		}
	}

	/**
	 * @param \ReflectionProperty $propertyReflection
	 * @return \Consistence\Sentry\Metadata\SentryIdentificator
	 */
	private function getSentryIdentificator(ReflectionProperty $propertyReflection)
	{
		try {
			$annotation = $this->annotationProvider->getPropertyAnnotation($propertyReflection, self::IDENTIFICATOR_ANNOTATION);
			return new SentryIdentificator($annotation->getValue());
		} catch (\Consistence\Annotation\AnnotationNotFoundException $e) {
			throw new \Consistence\Sentry\MetadataSource\Annotation\NoSentryIdentificatorException($e);
		}
	}

	/**
	 * @param \ReflectionProperty $propertyReflection
	 * @param \Consistence\Sentry\Type\Sentry $sentry
	 * @return \Consistence\Sentry\Metadata\SentryMethod[]
	 */
	private function createSentryMethods(ReflectionProperty $propertyReflection, Sentry $sentry)
	{
		$sentryMethods = [];
		foreach ($sentry->getSupportedAccess() as $sentryAccess) {
			$sentryMethods = array_merge($sentryMethods, $this->createSentryMethodsForAccess($propertyReflection, $sentry, $sentryAccess));
		}

		return $sentryMethods;
	}

	/**
	 * @param \ReflectionProperty $propertyReflection
	 * @param \Consistence\Sentry\Type\Sentry $sentry
	 * @param \Consistence\Sentry\Metadata\SentryAccess $sentryAccess
	 * @return \Consistence\Sentry\Metadata\SentryMethod[]
	 */
	private function createSentryMethodsForAccess(ReflectionProperty $propertyReflection, Sentry $sentry, SentryAccess $sentryAccess)
	{
		$sentryMethodsForAccess = [];
		$sentryAccessAnnotations = $this->annotationProvider->getPropertyAnnotations($propertyReflection, $sentryAccess->getName());
		foreach ($sentryAccessAnnotations as $sentryAccessAnnotation) {
			$sentryMethodsForAccess[] = $this->createSentryMethod($propertyReflection->getName(), $sentryAccess, $sentry, $sentryAccessAnnotation);
		}

		return $sentryMethodsForAccess;
	}

	/**
	 * @param string $propertyName
	 * @param \Consistence\Sentry\Metadata\SentryAccess $sentryAccess
	 * @param \Consistence\Sentry\Type\Sentry $sentry
	 * @param \Consistence\Annotation\Annotation $sentryAccessAnnotation
	 * @return \Consistence\Sentry\Metadata\SentryMethod
	 */
	private function createSentryMethod($propertyName, SentryAccess $sentryAccess, Sentry $sentry, Annotation $sentryAccessAnnotation)
	{
		try {
			$methodName = $sentryAccessAnnotation->getField(self::METHOD_PARAM_NAME)->getValue();
		} catch (\Consistence\Annotation\AnnotationFieldNotFoundException $e) {
			$methodName = $sentry->getDefaultMethodName($sentryAccess, $propertyName);
		}
		try {
			$methodVisibility = $sentryAccessAnnotation->getField(self::METHOD_PARAM_VISIBILITY)->getValue();
		} catch (\Consistence\Annotation\AnnotationFieldNotFoundException $e) {
			$methodVisibility = self::DEFAULT_VISIBILITY;
		}

		return new SentryMethod(
			$sentryAccess,
			$methodName,
			Visibility::get($methodVisibility)
		);
	}

}
