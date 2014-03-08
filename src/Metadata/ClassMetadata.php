<?php

namespace Consistence\Sentry\Metadata;

use Consistence\Type\Type;

class ClassMetadata extends \Consistence\ObjectPrototype
{

	/** @var string */
	private $name;

	/** @var \Consistence\Sentry\Metadata\PropertyMetadata[] */
	private $properties;

	/**
	 * @param string $name
	 * @param \Consistence\Sentry\Metadata\PropertyMetadata[] $properties
	 */
	public function __construct($name, array $properties)
	{
		Type::checkType($name, 'string');
		$this->name = $name;
		$this->properties = $properties;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return \Consistence\Sentry\Metadata\PropertyMetadata[]
	 */
	public function getProperties()
	{
		return $this->properties;
	}

}
