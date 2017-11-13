<?php

namespace Consistence\Sentry\MetadataSource;

use ReflectionClass;

class ClassMetadataCouldNotBeCreatedException extends \Consistence\PhpException implements \Consistence\Sentry\MetadataSource\Exception
{

	/** @var \ReflectionClass */
	private $classReflection;

	/**
	 * @param \ReflectionClass $classReflection
	 * @param string $message
	 * @param \Exception|null $previous
	 */
	public function __construct(ReflectionClass $classReflection, $message, \Exception $previous = null)
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

	/**
	 * @return \ReflectionClass
	 */
	public function getClassReflection()
	{
		return $this->classReflection;
	}

}
