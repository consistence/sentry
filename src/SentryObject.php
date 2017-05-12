<?php

declare(strict_types = 1);

namespace Consistence\Sentry;

use Consistence\Sentry\Runtime\RuntimeHelperBridge;
use Consistence\Type\ObjectMixin;

/**
 * @codeCoverageIgnore has dependency on global state
 */
abstract class SentryObject extends \Consistence\ObjectPrototype implements \Consistence\Sentry\SentryAware
{

	/**
	 * @param string $method
	 * @param mixed[] $args
	 * @return mixed
	 */
	public function __call($method, array $args)
	{
		$helper = RuntimeHelperBridge::getHelper();
		return $helper->run($this, $method, $args, function (SentryAware $object, $methodName) {
			ObjectMixin::magicCall($object, $methodName);
		});
	}

}
