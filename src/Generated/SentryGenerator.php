<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Generated;

use Consistence\ClassFinder\ClassFinder;
use Consistence\Reflection\ClassReflection;
use Consistence\Sentry\Factory\SentryFactory;
use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\MetadataSource\MetadataSource;
use Consistence\Sentry\SentryAware;
use ReflectionClass;

class SentryGenerator extends \Consistence\ObjectPrototype
{

	/** @var \Consistence\ClassFinder\ClassFinder */
	private $classFinder;

	/** @var \Consistence\Sentry\MetadataSource\MetadataSource */
	private $metadataSource;

	/** @var \Consistence\Sentry\Factory\SentryFactory */
	private $sentryFactory;

	/** @var string */
	private $targetDirectory;

	public function __construct(
		ClassFinder $classFinder,
		MetadataSource $metadataSource,
		SentryFactory $sentryFactory,
		string $targetDirectory
	)
	{
		$this->classFinder = $classFinder;
		$this->metadataSource = $metadataSource;
		$this->sentryFactory = $sentryFactory;
		$this->targetDirectory = $targetDirectory;
	}

	/**
	 * @return string[] className(string) => fileName(string)
	 */
	public function generateAll(): array
	{
		$classes = $this->classFinder->findByInterface(SentryAware::class);
		$generated = [];
		foreach ($classes as $className) {
			try {
				$classReflection = new ReflectionClass($className);
				$generated[$className] = $this->generateClass($classReflection);
			} catch (\Consistence\Sentry\Generated\NoMethodsToBeGeneratedException $e) {
				// skip
			}
		}

		return $generated;
	}

	/**
	 * @param \ReflectionClass $classReflection
	 * @return string fileName
	 */
	public function generateClass(ReflectionClass $classReflection): string
	{
		$classMetadata = $this->metadataSource->getMetadataForClass($classReflection);
		$methods = '';
		foreach ($classMetadata->getProperties() as $propertyMetadata) {
			$methods .= $this->generateMethods($classReflection, $propertyMetadata);
		}
		if ($methods === '') {
			throw new \Consistence\Sentry\Generated\NoMethodsToBeGeneratedException($classMetadata);
		}
		$classContent = file($classReflection->getFileName());
		$classEndLineNumber = $classReflection->getEndLine();
		$classContent[$classEndLineNumber - 1] = $methods . $classContent[$classEndLineNumber - 1];
		$file = implode('', $classContent);
		$fileName = $this->getFileName($classReflection->getName());
		file_put_contents($fileName, $file);

		return $fileName;
	}

	/**
	 * @param \ReflectionClass $classReflection
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $propertyMetadata
	 * @return string generated content
	 */
	private function generateMethods(ReflectionClass $classReflection, PropertyMetadata $propertyMetadata): string
	{
		$sentryIdentificator = $propertyMetadata->getSentryIdentificator();
		$sentry = $this->sentryFactory->getSentry($sentryIdentificator);

		$generatedMethods = '';
		foreach ($propertyMetadata->getSentryMethods() as $sentryMethod) {
			if (!ClassReflection::hasDeclaredMethod(
				$classReflection,
				$sentryMethod->getMethodName(),
				ClassReflection::CASE_INSENSITIVE
			)) {
				$generatedMethods .= $sentry->generateMethod($propertyMetadata, $sentryMethod) . "\n";
			}
		}

		return $generatedMethods;
	}

	private function getFileName(string $entityName): string
	{
		$name = str_replace('\\', '_', $entityName);
		$name = $this->targetDirectory . '/' . $name . '.php';

		return $name;
	}

}
