<?php

namespace Consistence\Sentry\Metadata;

use Consistence\Type\Type;

class PropertyMetadata extends \Consistence\ObjectPrototype
{

	/** @var string */
	private $name;

	/** @var string */
	private $className;

	/** @var string */
	private $type;

	/** @var \Consistence\Sentry\Metadata\SentryIdentificator */
	private $sentryIdentificator;

	/** @var boolean */
	private $nullable;

	/** @var \Consistence\Sentry\Metadata\SentryMethod[] */
	private $sentryMethods;

	/** @var \Consistence\Sentry\Metadata\BidirectionalAssociation|null */
	private $bidirectionalAssociation;

	/**
	 * @param string $name
	 * @param string $className
	 * @param string $type
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $sentryIdentificator
	 * @param boolean $nullable
	 * @param \Consistence\Sentry\Metadata\SentryMethod[] $sentryMethods
	 * @param \Consistence\Sentry\Metadata\BidirectionalAssociation|null $bidirectionalAssociation
	 */
	public function __construct(
		$name,
		$className,
		$type,
		SentryIdentificator $sentryIdentificator,
		$nullable,
		array $sentryMethods,
		BidirectionalAssociation $bidirectionalAssociation = null
	)
	{
		Type::checkType($name, 'string');
		Type::checkType($className, 'string');
		Type::checkType($type, 'string');
		Type::checkType($nullable, 'boolean');
		$this->name = $name;
		$this->className = $className;
		$this->type = $type;
		$this->sentryIdentificator = $sentryIdentificator;
		$this->nullable = $nullable;
		$this->sentryMethods = $sentryMethods;
		$this->bidirectionalAssociation = $bidirectionalAssociation;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getClassName()
	{
		return $this->className;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return \Consistence\Sentry\Metadata\SentryIdentificator
	 */
	public function getSentryIdentificator()
	{
		return $this->sentryIdentificator;
	}

	/**
	 * @return boolean
	 */
	public function isNullable()
	{
		return $this->nullable;
	}

	/**
	 * @return \Consistence\Sentry\Metadata\SentryMethod[]
	 */
	public function getSentryMethods()
	{
		return $this->sentryMethods;
	}

	/**
	 * @return \Consistence\Sentry\Metadata\BidirectionalAssociation|null
	 */
	public function getBidirectionalAssociation()
	{
		return $this->bidirectionalAssociation;
	}

}
