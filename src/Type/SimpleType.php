<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Type\Type;

class SimpleType extends \Consistence\Sentry\Type\AbstractSentry
{

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
