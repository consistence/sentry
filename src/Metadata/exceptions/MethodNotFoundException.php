<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

class MethodNotFoundException extends \Consistence\PhpException
{

	/** @var string */
	private $methodName;

	/** @var string */
	private $className;

	public function __construct(string $methodName, string $className, ?\Throwable $previous = null)
	{
		parent::__construct(sprintf('Method %s not found on %s', $methodName, $className), $previous);
		$this->methodName = $methodName;
		$this->className = $className;
	}

	public function getMethodName(): string
	{
		return $this->methodName;
	}

	public function getClassName(): string
	{
		return $this->className;
	}

}
