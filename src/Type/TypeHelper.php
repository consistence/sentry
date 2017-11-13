<?php

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

	/**
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $propertyMetadata
	 * @return string
	 */
	public static function getRequiredTypeString(PropertyMetadata $propertyMetadata)
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

	/**
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $propertyMetadata
	 * @return string
	 */
	public static function formatTypeToString(PropertyMetadata $propertyMetadata)
	{
		return (static::isObjectType($propertyMetadata->getType()) ? '\\' : '')
			. $propertyMetadata->getType()
			. ($propertyMetadata->isNullable() ? '|null' : '');
	}

	/**
	 * @param string $type
	 * @return boolean
	 */
	public static function isObjectType($type)
	{
		switch ($type) {
			case 'integer':
			case 'string':
			case 'boolean':
			case 'float':
			case 'int':
			case 'bool':
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

	/**
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param \Consistence\Sentry\Metadata\SentryMethod $sentryMethod
	 * @return string
	 */
	public static function generateGet(PropertyMetadata $property, SentryMethod $sentryMethod)
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

	/**
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param \Consistence\Sentry\Metadata\SentryMethod $sentryMethod
	 * @return string
	 */
	public static function generateSet(PropertyMetadata $property, SentryMethod $sentryMethod)
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
