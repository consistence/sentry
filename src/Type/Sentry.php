<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\BidirectionalAssociationType;
use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryAccess;
use Consistence\Sentry\Metadata\SentryMethod;

interface Sentry
{

	/**
	 * Get types of SentryAccess which can be handled by this Sentry
	 *
	 * @return \Consistence\Sentry\Metadata\SentryAccess[]
	 */
	public function getSupportedAccess(): iterable;

	/**
	 * Generate code for a SentryMethod
	 *
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param \Consistence\Sentry\Metadata\SentryMethod $sentryMethod
	 * @return string
	 */
	public function generateMethod(PropertyMetadata $property, SentryMethod $sentryMethod): string;

	/**
	 * Construct default method name for get set type and given property
	 *
	 * @param \Consistence\Sentry\Metadata\SentryAccess $sentryAccess
	 * @param string $propertyName
	 * @return string
	 */
	public function getDefaultMethodName(SentryAccess $sentryAccess, string $propertyName): string;

	/**
	 * Get types of SentryAccess which are needed to handle the target side of a bidirectional relationship
	 *
	 * @param \Consistence\Sentry\Metadata\SentryAccess $sentryAccess
	 * @param \Consistence\Sentry\Metadata\BidirectionalAssociationType $bidirectionalAssociationType
	 * @return \Consistence\Sentry\Metadata\SentryAccess[]
	 */
	public function getTargetAssociationAccessForAccess(SentryAccess $sentryAccess, BidirectionalAssociationType $bidirectionalAssociationType): iterable;

}
