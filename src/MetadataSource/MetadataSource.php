<?php

declare(strict_types = 1);

namespace Consistence\Sentry\MetadataSource;

use Consistence\Sentry\Metadata\ClassMetadata;

use ReflectionClass;

interface MetadataSource
{

	/**
	 * @param \ReflectionClass $classReflection
	 * @return \Consistence\Sentry\Metadata\ClassMetadata
	 * @throws \Consistence\Sentry\MetadataSource\ClassMetadataCouldNotBeCreatedException
	 */
	public function getMetadataForClass(ReflectionClass $classReflection): ClassMetadata;

}
