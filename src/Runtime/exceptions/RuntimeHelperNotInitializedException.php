<?php

namespace Consistence\Sentry\Runtime;

/**
 * @codeCoverageIgnore used only from static context
 */
class RuntimeHelperNotInitializedException extends \Consistence\PhpException implements \Consistence\Sentry\Runtime\Exception
{

	public function __construct(\Exception $previous = null)
	{
		parent::__construct('Sentry RuntimeHelper not initialized', $previous);
	}

}
