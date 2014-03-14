<?php

namespace Consistence\Sentry\MetadataSource;

use ReflectionClass;

interface MetadataSource
{

	/**
	 * @param \ReflectionClass $classReflection
	 * @return \Consistence\Sentry\Metadata\ClassMetadata
	 * @throws \Consistence\Sentry\MetadataSource\ClassMetadataCouldNotBeCreatedException
	 */
	public function getMetadataForClass(ReflectionClass $classReflection);

}
