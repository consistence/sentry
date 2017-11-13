<?php

namespace Consistence\Sentry\Metadata;

use Consistence\Type\Type;

class SentryAccess extends \Consistence\ObjectPrototype
{

	/** @var string */
	private $name;

	/**
	 * @param string $name
	 */
	public function __construct($name)
	{
		Type::checkType($name, 'string');
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param \Consistence\Sentry\Metadata\SentryAccess $to
	 * @return boolean
	 */
	public function equals(SentryAccess $to)
	{
		return $this->getName() === $to->getName();
	}

}
