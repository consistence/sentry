<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\SentryAware;
use ReflectionProperty;

class TypeHelper extends \Consistence\ObjectPrototype
{

	final public function __construct()
	{
		throw new \Consistence\StaticClassException();
	}

	public static function getRequiredTypeString(PropertyMetadata $propertyMetadata): string
	{
		return $propertyMetadata->getType() . ($propertyMetadata->isNullable() ? '|null' : '');
	}

	/**
	 * @param mixed[] $args
	 * @return mixed first argument
	 */
	public static function getFirstArg(array $args)
	{
		if (!array_key_exists(0, $args)) {
			throw new \Consistence\Sentry\Type\MissingArgumentException($args, 1);
		}

		return $args[0];
	}

	public static function formatTypeToString(PropertyMetadata $propertyMetadata): string
	{
		return (static::isObjectType($propertyMetadata->getType()) ? '\\' : '')
			. $propertyMetadata->getType()
			. ($propertyMetadata->isNullable() ? '|null' : '');
	}

	public static function isObjectType(string $type): bool
	{
		switch ($type) {
			case 'int':
			case 'string':
			case 'bool':
			case 'float':
			case 'integer':
			case 'boolean':
			case 'array':
			case 'mixed':
				return false;
			default:
				return true;
		}
	}

	/**
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $propertyMetadata
	 * @param \Consistence\Sentry\SentryAware $object
	 * @return mixed
	 */
	public static function getPropertyValue(PropertyMetadata $propertyMetadata, SentryAware $object)
	{
		$propertyReflection = new ReflectionProperty($propertyMetadata->getClassName(), $propertyMetadata->getName());
		$propertyReflection->setAccessible(true);

		return $propertyReflection->getValue($object);
	}

	/**
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $propertyMetadata
	 * @param \Consistence\Sentry\SentryAware $object
	 * @param mixed $value
	 */
	public static function setPropertyValue(PropertyMetadata $propertyMetadata, SentryAware $object, $value)
	{
		$propertyReflection = new ReflectionProperty($propertyMetadata->getClassName(), $propertyMetadata->getName());
		$propertyReflection->setAccessible(true);
		$propertyReflection->setValue($object, $value);
	}

	public static function generateGet(PropertyMetadata $property, SentryMethod $sentryMethod): string
	{
		return '
	/**
	 * Generated ' . $property->getType() . ' getter
	 *
	 * @return ' . static::formatTypeToString($property) . '
	 */
	' . $sentryMethod->getMethodVisibility()->getName() . ' function ' . $sentryMethod->getMethodName() . '()
	{
		return $this->' . $property->getName() . ';
	}';
	}

	public static function generateSet(PropertyMetadata $property, SentryMethod $sentryMethod): string
	{
		$method = '
	/**
	 * Generated ' . $property->getType() . ' setter
	 *
	 * @param ' . static::formatTypeToString($property) . ' $newValue
	 */
	' . $sentryMethod->getMethodVisibility()->getName() . ' function ' . $sentryMethod->getMethodName() . '($newValue)
	{
		$this->' . $property->getName() . ' = $newValue;
	}';

		return $method;
	}

}
