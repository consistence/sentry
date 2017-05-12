<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Runtime;

/**
 * @codeCoverageIgnore used only from static context
 */
class RuntimeHelperNotInitializedException extends \Consistence\PhpException
{

	public function __construct(\Throwable $previous = null)
	{
		parent::__construct('Sentry RuntimeHelper not initialized', $previous);
	}

}
