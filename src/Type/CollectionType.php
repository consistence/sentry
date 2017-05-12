<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryAccess;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\SentryAware;
use Consistence\Type\ArrayType\ArrayType;
use Consistence\Type\Type;

use Doctrine\Common\Inflector\Inflector;

class CollectionType extends \Consistence\Sentry\Type\AbstractSentry
{

	const ADD = 'add';
	const CONTAINS = 'contains';
	const REMOVE = 'remove';

	/**
	 * @return \Consistence\Sentry\Metadata\SentryAccess[]
	 */
	public function getSupportedAccess()
	{
		return [
			new SentryAccess(self::GET),
			new SentryAccess(self::SET),
			new SentryAccess(self::ADD),
			new SentryAccess(self::REMOVE),
			new SentryAccess(self::CONTAINS),
		];
	}

	public function getDefaultMethodName(SentryAccess $sentryAccess, string $propertyName): string
	{
		switch ($sentryAccess->getName()) {
			case self::ADD:
				return 'add' . ucfirst(Inflector::singularize($propertyName));
			case self::REMOVE:
				return 'remove' . ucfirst(Inflector::singularize($propertyName));
			case self::CONTAINS:
				return 'contains' . ucfirst(Inflector::singularize($propertyName));
			default:
				return parent::getDefaultMethodName($sentryAccess, $propertyName);
		}
	}

	/**
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param \Consistence\Sentry\SentryAware $object
	 * @param mixed[] $args
	 */
	public function set(PropertyMetadata $property, SentryAware $object, array $args)
	{
		$newValues = TypeHelper::getFirstArg($args);
		Type::checkType($newValues, 'array');

		$collection = [];
		foreach ($newValues as $item) {
			$this->addValue($collection, $property, $item);
		}
		TypeHelper::setPropertyValue($property, $object, $collection);
	}

	/**
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param \Consistence\Sentry\SentryAware $object
	 * @param mixed[] $args
	 * @return boolean was element really added?
	 */
	public function add(PropertyMetadata $property, SentryAware $object, array $args): bool
	{
		$value = TypeHelper::getFirstArg($args);

		$collection = TypeHelper::getPropertyValue($property, $object);
		$changed = $this->addValue($collection, $property, $value);
		TypeHelper::setPropertyValue($property, $object, $collection);

		return $changed;
	}

	/**
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param \Consistence\Sentry\SentryAware $object
	 * @param mixed[] $args
	 * @return boolean was element really removed?
	 */
	public function remove(PropertyMetadata $property, SentryAware $object, array $args): bool
	{
		$value = TypeHelper::getFirstArg($args);

		$collection = TypeHelper::getPropertyValue($property, $object);
		$changed = $this->removeValue($collection, $property, $value);
		TypeHelper::setPropertyValue($property, $object, $collection);

		return $changed;
	}

	/**
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param \Consistence\Sentry\SentryAware $object
	 * @param mixed[] $args
	 * @return boolean
	 */
	public function contains(PropertyMetadata $property, SentryAware $object, array $args): bool
	{
		$value = TypeHelper::getFirstArg($args);
		Type::checkType($value, $property->getType());
		$collection = TypeHelper::getPropertyValue($property, $object);

		return ArrayType::containsValue($collection, $value);
	}

	/**
	 * @param mixed[] $collection
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param mixed $value
	 * @return boolean was element really added?
	 */
	private function addValue(array &$collection, PropertyMetadata $property, $value): bool
	{
		Type::checkType($value, $property->getType());
		if (ArrayType::containsValue($collection, $value)) {
			return false;
		}
		$collection[] = $value;

		return true;
	}

	/**
	 * @param mixed[] $collection
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param mixed $value
	 * @return boolean was element really removed?
	 */
	private function removeValue(array &$collection, PropertyMetadata $property, $value): bool
	{
		Type::checkType($value, $property->getType());
		return ArrayType::removeValue($collection, $value);
	}

	public function generateGet(PropertyMetadata $property, SentryMethod $sentryMethod): string
	{
		return '
	/**
	 * Generated ' . $property->getType() . ' collection getter
	 *
	 * @return ' . TypeHelper::formatTypeToString($property) . '[]
	 */
	' . $sentryMethod->getMethodVisibility()->getName() . ' function ' . $sentryMethod->getMethodName() . '()
	{
		return $this->' . $property->getName() . ';
	}';
	}

	public function generateSet(PropertyMetadata $property, SentryMethod $sentryMethod): string
	{
		$method = '
	/**
	 * Generated ' . $property->getType() . ' collection setter
	 *
	 * @param ' . TypeHelper::formatTypeToString($property) . '[] $newValues
	 */
	' . $sentryMethod->getMethodVisibility()->getName() . ' function ' . $sentryMethod->getMethodName() . '($newValues)
	{
		\\' . Type::class . '::checkType($newValues, \'array\');
		$collection =& $this->' . $property->getName() . ';
		$collection = [];
		foreach ($newValues as $el) {
			\\' . Type::class . '::checkType($el, \'' . $property->getType() . '\');
			if (!\\' . ArrayType::class . '::containsValue($collection, $el)) {
				$collection[] = $el;
			}
		}
	}';

		return $method;
	}

	public function generateContains(PropertyMetadata $property, SentryMethod $sentryMethod): string
	{
		return '
	/**
	 * Generated ' . $property->getType() . ' collection contains
	 *
	 * @param ' . TypeHelper::formatTypeToString($property) . ' $value
	 * @return boolean
	 */
	' . $sentryMethod->getMethodVisibility()->getName() . ' function ' . $sentryMethod->getMethodName() . '($value)
	{
		\\' . Type::class . '::checkType($value, \'' . $property->getType() . '\');
		return \\' . ArrayType::class . '::containsValue($this->' . $property->getName() . ', $value);
	}';
	}

	public function generateAdd(PropertyMetadata $property, SentryMethod $sentryMethod): string
	{
		$method = '
	/**
	 * Generated ' . $property->getType() . ' collection add
	 *
	 * @param ' . TypeHelper::formatTypeToString($property) . ' $newValue
	 * @return boolean was element really added?
	 */
	' . $sentryMethod->getMethodVisibility()->getName() . ' function ' . $sentryMethod->getMethodName() . '($newValue)
	{
		\\' . Type::class . '::checkType($newValue, \'' . $property->getType() . '\');
		$collection =& $this->' . $property->getName() . ';
		if (!\\' . ArrayType::class . '::containsValue($collection, $newValue)) {
			$collection[] = $newValue;

			return true;
		}

		return false;
	}';

		return $method;
	}

	public function generateRemove(PropertyMetadata $property, SentryMethod $sentryMethod): string
	{
		$method = '
	/**
	 * Generated ' . $property->getType() . ' collection remove
	 *
	 * @param ' . TypeHelper::formatTypeToString($property) . ' $value
	 * @return boolean was element really removed?
	 */
	' . $sentryMethod->getMethodVisibility()->getName() . ' function ' . $sentryMethod->getMethodName() . '($value)
	{
		\\' . Type::class . '::checkType($value, \'' . $property->getType() . '\');
		return \\' . ArrayType::class . '::removeValue($this->' . $property->getName() . ', $value);
	}';

		return $method;
	}

}
