<?php

declare(strict_types = 1);

namespace Consistence\Sentry\MetadataSource;

use ReflectionClass;

class ClassMetadataCouldNotBeCreatedException extends \Consistence\PhpException
{

	/** @var \ReflectionClass */
	private $classReflection;

	public function __construct(ReflectionClass $classReflection, string $message, ?\Throwable $previous = null)
	{
		parent::__construct(
			sprintf(
				'%s: %s',
				$classReflection->getName(),
				$message
			),
			$previous
		);
		$this->classReflection = $classReflection;
	}

	public function getClassReflection(): ReflectionClass
	{
		return $this->classReflection;
	}

}
