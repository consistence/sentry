<?php

namespace Consistence\Sentry\MetadataSource\Annotation;

class PropertyMetadataCouldNotBeCreatedException extends \Consistence\PhpException implements \Consistence\Sentry\MetadataSource\Annotation\Exception
{

	public function __construct(\Exception $previous = null)
	{
		parent::__construct('', $previous);
	}

}
