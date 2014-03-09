<?php

namespace Consistence\Sentry\Type;

use Consistence\Sentry\Metadata\BidirectionalAssociationType;
use Consistence\Sentry\Metadata\PropertyMetadata;
use Consistence\Sentry\Metadata\SentryAccess;
use Consistence\Sentry\Metadata\SentryMethod;
use Consistence\Sentry\SentryAware;

interface Sentry
{

	/**
	 * Get types of SentryAccess which can be handled by this Sentry
	 *
	 * @return \Consistence\Sentry\Metadata\SentryAccess[]
	 */
	public function getSupportedAccess();

	/**
	 * Logic which should be executed while processing SentryMethods in runtime
	 *
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param \Consistence\Sentry\SentryAware $object
	 * @param \Consistence\Sentry\Metadata\SentryMethod $sentryMethod
	 * @param mixed[] $args arguments given to called method
	 * @return mixed
	 */
	public function processMethod(PropertyMetadata $property, SentryAware $object, SentryMethod $sentryMethod, array $args);

	/**
	 * Generate code for a SentryMethod
	 *
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata $property
	 * @param \Consistence\Sentry\Metadata\SentryMethod $sentryMethod
	 * @return string
	 */
	public function generateMethod(PropertyMetadata $property, SentryMethod $sentryMethod);

	/**
	 * Construct default method name for get set type and given property
	 *
	 * @param \Consistence\Sentry\Metadata\SentryAccess $sentryAccess
	 * @param string $propertyName
	 * @return string
	 */
	public function getDefaultMethodName(SentryAccess $sentryAccess, $propertyName);

	/**
	 * Get types of SentryAccess which are needed to handle the target side of a bidirectional relationship
	 *
	 * @param \Consistence\Sentry\Metadata\SentryAccess $sentryAccess
	 * @param \Consistence\Sentry\Metadata\BidirectionalAssociationType $bidirectionalAssociationType
	 * @return \Consistence\Sentry\Metadata\SentryAccess[]
	 */
	public function getTargetAssociationAccessForAccess(SentryAccess $sentryAccess, BidirectionalAssociationType $bidirectionalAssociationType);

}
