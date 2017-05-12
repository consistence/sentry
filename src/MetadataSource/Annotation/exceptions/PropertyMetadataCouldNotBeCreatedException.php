<?php

declare(strict_types = 1);

namespace Consistence\Sentry\MetadataSource\Annotation;

class PropertyMetadataCouldNotBeCreatedException extends \Consistence\PhpException
{

	public function __construct(\Throwable $previous = null)
	{
		parent::__construct('', $previous);
	}

}
