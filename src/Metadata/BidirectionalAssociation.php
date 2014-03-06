<?php

namespace Consistence\Sentry\Metadata;

use Consistence\Type\ArrayType\ArrayType;
use Consistence\Type\Type;

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
		$targetClass,
		$targetProperty,
		BidirectionalAssociationType $targetType,
		array $targetMethods
	)
	{
		Type::checkType($targetClass, 'string');
		Type::checkType($targetProperty, 'string');
		$this->targetClass = $targetClass;
		$this->targetProperty = $targetProperty;
		$this->targetType = $targetType;
		$this->targetMethods = $targetMethods;
	}

	/**
	 * @return string
	 */
	public function getTargetClass()
	{
		return $this->targetClass;
	}

	/**
	 * @return string
	 */
	public function getTargetProperty()
	{
		return $this->targetProperty;
	}

	/**
	 * @return \Consistence\Sentry\Metadata\BidirectionalAssociationType
	 */
	public function getTargetType()
	{
		return $this->targetType;
	}

	/**
	 * @param \Consistence\Sentry\Metadata\SentryAccess $sentryAccess
	 * @param \Consistence\Sentry\Metadata\Visibility $requiredVisibility
	 * @return \Consistence\Sentry\Metadata\SentryMethod
	 */
	public function getTargetMethodForType(SentryAccess $sentryAccess, Visibility $requiredVisibility)
	{
		try {
			return ArrayType::getValueByCallback(
				$this->targetMethods,
				function (SentryMethod $targetMethod) use ($sentryAccess, $requiredVisibility) {
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
