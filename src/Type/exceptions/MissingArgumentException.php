<?php

namespace Consistence\Sentry\Type;

class MissingArgumentException extends \Consistence\PhpException implements \Consistence\Sentry\Type\Exception
{

	/** @var mixed[] */
	private $args;

	/** @var integer */
	private $requiredCountOfArguments;

	/**
	 * @param mixed[] $args
	 * @param integer $requiredCountOfArguments
	 * @param \Exception|null $previous
	 */
	public function __construct(array $args, $requiredCountOfArguments, \Exception $previous = null)
	{
		parent::__construct(
			sprintf('Sentry requires at least %d arguments, %d given', $requiredCountOfArguments, count($args)),
			$previous
		);
		$this->args = $args;
		$this->requiredCountOfArguments = $requiredCountOfArguments;
	}

	/**
	 * @return mixed[]
	 */
	public function getArgs()
	{
		return $this->args;
	}

	/**
	 * @return integer
	 */
	public function getRequiredCountOfArguments()
	{
		return $this->requiredCountOfArguments;
	}

}
