<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Generated;

use Consistence\Sentry\Metadata\ClassMetadata;

class NoMethodsToBeGeneratedException extends \Consistence\PhpException implements \Consistence\Sentry\Generated\Exception
{

	/** @var \Consistence\Sentry\Metadata\ClassMetadata */
	private $classMetadata;

	public function __construct(ClassMetadata $classMetadata, \Throwable $previous = null)
	{
		parent::__construct(
			sprintf('Class %s has no methods to be generated', $classMetadata->getName()),
			$previous
		);
		$this->classMetadata = $classMetadata;
	}

	public function getClassMetadata(): ClassMetadata
	{
		return $this->classMetadata;
	}

}
