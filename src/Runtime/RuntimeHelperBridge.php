<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Runtime;

/**
 * Static holder of RuntimeHelper
 *
 * This class is accessed from places such as SentryObject::__call method,
 * where the dependency cannot be given via constructor.
 * Helper should be set in Bootstrap.
 *
 * @codeCoverageIgnore static class representing global state
 */
class RuntimeHelperBridge extends \Consistence\ObjectPrototype
{

	/** @var \Consistence\Sentry\Runtime\RuntimeHelper */
	private static $runtimeHelper;

	private function __construct()
	{
		throw new \Consistence\StaticClassException();
	}

	public static function setHelper(RuntimeHelper $runtimeHelper)
	{
		self::$runtimeHelper = $runtimeHelper;
	}

	public static function getHelper(): RuntimeHelper
	{
		if (self::$runtimeHelper === null) {
			throw new \Consistence\Sentry\Runtime\RuntimeHelperNotInitializedException();
		}

		return self::$runtimeHelper;
	}

}
