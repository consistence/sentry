<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

use Consistence\Type\ArrayType\ArrayType;

class PropertyMetadata extends \Consistence\ObjectPrototype
{

	/** @var string */
	private $name;

	/** @var string */
	private $className;

	/** @var string */
	private $type;

	/** @var \Consistence\Sentry\Metadata\SentryIdentificator */
	private $sentryIdentificator;

	/** @var boolean */
	private $nullable;

	/** @var \Consistence\Sentry\Metadata\SentryMethod[] */
	private $sentryMethods;

	/** @var \Consistence\Sentry\Metadata\BidirectionalAssociation|null */
	private $bidirectionalAssociation;

	/**
	 * @param string $name
	 * @param string $className
	 * @param string $type
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $sentryIdentificator
	 * @param boolean $nullable
	 * @param \Consistence\Sentry\Metadata\SentryMethod[] $sentryMethods
	 * @param \Consistence\Sentry\Metadata\BidirectionalAssociation|null $bidirectionalAssociation
	 */
	public function __construct(
		string $name,
		string $className,
		string $type,
		SentryIdentificator $sentryIdentificator,
		bool $nullable,
		array $sentryMethods,
		BidirectionalAssociation $bidirectionalAssociation = null
	)
	{
		$this->name = $name;
		$this->className = $className;
		$this->type = $type;
		$this->sentryIdentificator = $sentryIdentificator;
		$this->nullable = $nullable;
		$this->sentryMethods = $sentryMethods;
		$this->bidirectionalAssociation = $bidirectionalAssociation;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getClassName(): string
	{
		return $this->className;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getSentryIdentificator(): SentryIdentificator
	{
		return $this->sentryIdentificator;
	}

	public function isNullable(): bool
	{
		return $this->nullable;
	}

	/**
	 * @return \Consistence\Sentry\Metadata\SentryMethod[]
	 */
	public function getSentryMethods(): array
	{
		return $this->sentryMethods;
	}

	/**
	 * @return \Consistence\Sentry\Metadata\BidirectionalAssociation|null
	 */
	public function getBidirectionalAssociation()
	{
		return $this->bidirectionalAssociation;
	}

	public function getSentryMethodByAccessAndRequiredVisibility(
		SentryAccess $sentryAccess,
		Visibility $requiredVisibility
	): SentryMethod
	{
		try {
			return ArrayType::getValueByCallback(
				$this->getSentryMethods(),
				function (SentryMethod $sentryMethod) use ($sentryAccess, $requiredVisibility): bool {
					return $sentryMethod->getSentryAccess()->equals($sentryAccess)
						&& $sentryMethod->getMethodVisibility()->isLooserOrEqualTo($requiredVisibility);
				}
			);
		} catch (\Consistence\Type\ArrayType\ElementDoesNotExistException $e) {
			throw new \Consistence\Sentry\Metadata\NoSuitableMethodException(
				$this->getClassName(),
				$this->getName(),
				$sentryAccess,
				$e
			);
		}
	}

	/**
	 * Method name is compared case-insensitive to be consistent with PHP behaviour
	 *
	 * @param string $methodName
	 * @param \Consistence\Sentry\Metadata\Visibility $requiredVisibility
	 * @return \Consistence\Sentry\Metadata\SentryMethod
	 */
	public function getSentryMethodByNameAndRequiredVisibility(
		string $methodName,
		Visibility $requiredVisibility
	): SentryMethod
	{
		try {
			return ArrayType::getValueByCallback(
				$this->getSentryMethods(),
				function (SentryMethod $sentryMethod) use ($methodName, $requiredVisibility): bool {
					return strcasecmp($sentryMethod->getMethodName(), $methodName) === 0
						&& $sentryMethod->getMethodVisibility()->isLooserOrEqualTo($requiredVisibility);
				}
			);
		} catch (\Consistence\Type\ArrayType\ElementDoesNotExistException $e) {
			throw new \Consistence\Sentry\Metadata\MethodNotFoundForPropertyException(
				$methodName,
				$this->getClassName(),
				$this->getName(),
				$e
			);
		}
	}

	/**
	 * @return \Consistence\Sentry\Metadata\SentryAccess[]
	 */
	public function getDefinedSentryAccess()
	{
		$sentryAccess = [];
		foreach ($this->getSentryMethods() as $sentryMethod) {
			$sentryAccessName = $sentryMethod->getSentryAccess()->getName();
			if (!isset($sentryAccess[$sentryAccessName])) {
				$sentryAccess[$sentryAccessName] = $sentryMethod->getSentryAccess();
			}
		}

		return array_values($sentryAccess);
	}

}
