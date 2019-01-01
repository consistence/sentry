<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\BidirectionalAssociationType;
use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryAccess;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Type\ArrayType\ArrayType;

/**
 * Provides default implementation(get, set) of a Sentry type, although no checks are performed
 */
abstract class AbstractSentry extends \Consistence\ObjectPrototype implements \Consistence\Sentry\Type\Sentry
{

	public const GET = 'get';
	public const SET = 'set';

	/**
	 * @return \Consistence\Sentry\Metadata\SentryAccess[]
	 */
	public function getSupportedAccess(): array
	{
		return [
			new SentryAccess(self::GET),
			new SentryAccess(self::SET),
		];
	}

	/**
	 * Redirects generate requests to methods named generate{SentryAccess}(...)
	 *
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param \Consistence\Sentry\Metadata\SentryMethod $sentryMethod
	 * @return string
	 */
	public function generateMethod(PropertyMetadata $property, SentryMethod $sentryMethod): string
	{
		$this->checkSupportedSentryAccess($property, $sentryMethod->getSentryAccess());

		$callMethod = 'generate' . ucfirst($sentryMethod->getSentryAccess()->getName());

		return $this->$callMethod($property, $sentryMethod);
	}

	protected function checkSupportedSentryAccess(PropertyMetadata $property, SentryAccess $requiredSentryAccess): void
	{
		try {
			ArrayType::getValueByCallback(
				static::getSupportedAccess(),
				function (SentryAccess $sentryAccess) use ($requiredSentryAccess): bool {
					return $requiredSentryAccess->equals($sentryAccess);
				}
			);
		} catch (\Consistence\Type\ArrayType\ElementDoesNotExistException $e) {
			throw new \Consistence\Sentry\Type\SentryAccessNotSupportedForPropertyException($property, $requiredSentryAccess, get_class($this), $e);
		}
	}

	public function getDefaultMethodName(SentryAccess $sentryAccess, string $propertyName): string
	{
		switch ($sentryAccess->getName()) {
			case self::GET:
				return 'get' . ucfirst($propertyName);
			case self::SET:
				return 'set' . ucfirst($propertyName);
			default:
				throw new \Consistence\Sentry\Type\SentryAccessNotSupportedException($sentryAccess, get_class($this));
		}
	}

	/**
	 * This Sentry does not support bidirectional relations by default, so an empty array can be always returned
	 *
	 * @param \Consistence\Sentry\Metadata\SentryAccess $sentryAccess
	 * @param \Consistence\Sentry\Metadata\BidirectionalAssociationType $bidirectionalAssociationType
	 * @return \Consistence\Sentry\Metadata\SentryAccess[]
	 */
	public function getTargetAssociationAccessForAccess(SentryAccess $sentryAccess, BidirectionalAssociationType $bidirectionalAssociationType): array
	{
		return [];
	}

	public function generateGet(PropertyMetadata $property, SentryMethod $sentryMethod): string
	{
		return TypeHelper::generateGet($property, $sentryMethod);
	}

	public function generateSet(PropertyMetadata $property, SentryMethod $sentryMethod): string
	{
		return TypeHelper::generateSet($property, $sentryMethod);
	}

}
