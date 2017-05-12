<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

use Consistence\Type\ArrayType\ArrayType;

class BidirectionalAssociation extends \Consistence\ObjectPrototype
{

	/** @var string */
	private $targetClass;

	/** @var string */
	private $targetProperty;

	/** @var \Consistence\Sentry\Metadata\BidirectionalAssociationType */
	private $targetType;

	/** @var \Consistence\Sentry\Metadata\SentryMethod[] */
	private $targetMethods;

	/**
	 * @param string $targetClass
	 * @param string $targetProperty
	 * @param \Consistence\Sentry\Metadata\BidirectionalAssociationType $targetType
	 * @param \Consistence\Sentry\Metadata\SentryMethod[] $targetMethods
	 */
	public function __construct(
		string $targetClass,
		string $targetProperty,
		BidirectionalAssociationType $targetType,
		array $targetMethods
	)
	{
		$this->targetClass = $targetClass;
		$this->targetProperty = $targetProperty;
		$this->targetType = $targetType;
		$this->targetMethods = $targetMethods;
	}

	public function getTargetClass(): string
	{
		return $this->targetClass;
	}

	public function getTargetProperty(): string
	{
		return $this->targetProperty;
	}

	public function getTargetType(): BidirectionalAssociationType
	{
		return $this->targetType;
	}

	public function getTargetMethodForType(SentryAccess $sentryAccess, Visibility $requiredVisibility): SentryMethod
	{
		try {
			return ArrayType::getValueByCallback(
				$this->targetMethods,
				function (SentryMethod $targetMethod) use ($sentryAccess, $requiredVisibility): bool {
					return $targetMethod->getSentryAccess()->equals($sentryAccess)
						&& $targetMethod->getMethodVisibility()->isLooserOrEqualTo($requiredVisibility);
				}
			);
		} catch (\Consistence\Type\ArrayType\ElementDoesNotExistException $e) {
			throw new \Consistence\Sentry\Metadata\NoSuitableMethodException(
				$this->targetClass,
				$this->targetProperty,
				$sentryAccess,
				$e
			);
		}
	}

}
