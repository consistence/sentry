<?php

namespace Consistence\Sentry\Metadata;

use Consistence\Type\Type;

class Visibility extends \Consistence\Enum\Enum
{

	const VISIBILITY_PUBLIC = 'public';
	const VISIBILITY_PROTECTED = 'protected';
	const VISIBILITY_PRIVATE = 'private';

	/** @var integer[] */
	private static $comparationValues = [
		self::VISIBILITY_PRIVATE => 1,
		self::VISIBILITY_PROTECTED => 2,
		self::VISIBILITY_PUBLIC => 3,
	];

	/**
	 * @param static $visibility
	 * @return boolean
	 */
	public function isLooserOrEqualTo(self $visibility)
	{
		return self::$comparationValues[$this->getValue()] >= self::$comparationValues[$visibility->getValue()];
	}

	/**
	 * Returns the minimal required visibility required by the relation of the two classes
	 *
	 * @param string $targetClassName
	 * @param string $originClassName
	 * @return self
	 */
	public static function getRequiredVisibility($targetClassName, $originClassName)
	{
		Type::checkType($targetClassName, 'string');
		Type::checkType($originClassName, 'string');

		if ($targetClassName === $originClassName) {
			return self::get(self::VISIBILITY_PRIVATE);
		}
		if (is_subclass_of($originClassName, $targetClassName) || is_subclass_of($targetClassName, $originClassName)) {
			return self::get(self::VISIBILITY_PROTECTED);
		}

		return self::get(self::VISIBILITY_PUBLIC);
	}

}
