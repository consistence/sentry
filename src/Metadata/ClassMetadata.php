<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

use Consistence\Type\ArrayType\ArrayType;

class ClassMetadata extends \Consistence\ObjectPrototype
{

	/** @var string */
	private $name;

	/** @var \Consistence\Sentry\Metadata\PropertyMetadata[] */
	private $properties;

	/**
	 * @param string $name
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata[] $properties
	 */
	public function __construct(string $name, array $properties)
	{
		$this->name = $name;
		$this->properties = $properties;
	}

	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return \Consistence\Sentry\Metadata\PropertyMetadata[]
	 */
	public function getProperties(): array
	{
		return $this->properties;
	}

	public function getPropertyByName(string $propertyName): PropertyMetadata
	{
		try {
			return ArrayType::getValueByCallback(
				$this->getProperties(),
				function (PropertyMetadata $propertyMetadata) use ($propertyName): bool {
					return $propertyMetadata->getName() === $propertyName;
				}
			);
		} catch (\Consistence\Type\ArrayType\ElementDoesNotExistException $e) {
			throw new \Consistence\Sentry\Metadata\PropertyNotFoundException($this->getName(), $propertyName, $e);
		}
	}

	public function getSentryMethodByNameAndRequiredVisibility(
		string $methodName,
		Visibility $requiredVisibility
	): SentryMethodSearchResult
	{
		foreach ($this->getProperties() as $property) {
			try {
				$sentryMethod = $property->getSentryMethodByNameAndRequiredVisibility($methodName, $requiredVisibility);
				return new SentryMethodSearchResult($sentryMethod, $property);
			} catch (\Consistence\Sentry\Metadata\MethodNotFoundForPropertyException $e) {
				// continue
			}
		}

		throw new \Consistence\Sentry\Metadata\MethodNotFoundException($methodName, $this->getName());
	}

}
