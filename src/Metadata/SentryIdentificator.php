<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Metadata;

class SentryIdentificator extends \Consistence\ObjectPrototype
{

	/** @var mixed */
	private $id;

	/**
	 * @param mixed $id
	 */
	public function __construct($id)
	{
		$this->id = $id;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	public function equals(SentryIdentificator $to): bool
	{
		return $this->getId() === $to->getId();
	}

}
