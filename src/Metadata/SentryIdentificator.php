<?php

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

	/**
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $to
	 * @return boolean
	 */
	public function equals(SentryIdentificator $to)
	{
		return $this->getId() === $to->getId();
	}

}
