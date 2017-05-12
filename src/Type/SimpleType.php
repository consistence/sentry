<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\SentryAware;
use Consistence\Type\Type;

class SimpleType extends \Consistence\Sentry\Type\AbstractSentry
{

	/**
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param \Consistence\Sentry\SentryAware $object
	 * @param mixed[] $args
	 */
	public function set(PropertyMetadata $property, SentryAware $object, array $args)
	{
		$value = TypeHelper::getFirstArg($args);
		Type::checkType($value, TypeHelper::getRequiredTypeString($property));
		TypeHelper::setPropertyValue($property, $object, $value);
	}

	public function generateSet(PropertyMetadata $property, SentryMethod $sentryMethod): string
	{
		$method = '
	/**
	 * Generated ' . $property->getType() . ' setter
	 *
	 * @param ' . TypeHelper::formatTypeToString($property) . ' $newValue
	 */
	' . $sentryMethod->getMethodVisibility()->getName() . ' function ' . $sentryMethod->getMethodName() . '($newValue)
	{
		\\' . Type::class . '::checkType($newValue, \'' . TypeHelper::getRequiredTypeString($property) . '\');
		$this->' . $property->getName() . ' = $newValue;
	}';

		return $method;
	}

}
