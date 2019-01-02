<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Type;

class FooClass extends \Consistence\ObjectPrototype implements \Consistence\Sentry\SentryAware
{

	/** @var mixed */
	private $fooProperty;

	/** @var mixed[] */
	private $children;

	public function __construct()
	{
		$this->children = [];
	}

}
