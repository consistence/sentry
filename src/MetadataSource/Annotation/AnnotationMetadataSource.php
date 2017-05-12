<?php

declare(strict_types = 1);

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

	public function getMetadataForClass(ReflectionClass $classReflection): ClassMetadata
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

	private function createClassMetadata(ReflectionClass $classReflection): ClassMetadata
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

	private function createPropertyMetadata(ReflectionProperty $propertyReflection): PropertyMetadata
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

	private function getSentryIdentificator(ReflectionProperty $propertyReflection): SentryIdentificator
	{
		try {
			$annotation = $this->annotationProvider->getPropertyAnnotation($propertyReflection, self::IDENTIFICATOR_ANNOTATION);
			$sentryIdentificatorString = $propertyReflection->class . SentryIdentificatorParser::SOURCE_CLASS_SEPARATOR . $annotation->getValue();
			return new SentryIdentificator($sentryIdentificatorString);
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

	private function createSentryMethod(
		string $propertyName,
		SentryAccess $sentryAccess,
		Sentry $sentry,
		Annotation $sentryAccessAnnotation
	): SentryMethod
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
