<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

class Visibility extends \Consistence\Enum\Enum
{

	public const VISIBILITY_PUBLIC = 'public';
	public const VISIBILITY_PROTECTED = 'protected';
	public const VISIBILITY_PRIVATE = 'private';

	private const COMPARATION_VALUES = [
		self::VISIBILITY_PRIVATE => 1,
		self::VISIBILITY_PROTECTED => 2,
		self::VISIBILITY_PUBLIC => 3,
	];

	public function getName(): string
	{
		return $this->getValue();
	}

	public function isLooserOrEqualTo(self $visibility): bool
	{
		return self::COMPARATION_VALUES[$this->getValue()] >= self::COMPARATION_VALUES[$visibility->getValue()];
	}

	/**
	 * Returns the minimal required visibility required by the relation of the two classes
	 *
	 * @param string $targetClassName
	 * @param string $originClassName
	 * @return self
	 */
	public static function getRequiredVisibility(string $targetClassName, string $originClassName): self
	{
		if ($targetClassName === $originClassName) {
			return self::get(self::VISIBILITY_PRIVATE);
		}
		if (is_subclass_of($originClassName, $targetClassName) || is_subclass_of($targetClassName, $originClassName)) {
			return self::get(self::VISIBILITY_PROTECTED);
		}

		return self::get(self::VISIBILITY_PUBLIC);
	}

}
