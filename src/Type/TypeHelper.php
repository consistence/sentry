<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryMethod;

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
