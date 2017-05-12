<?php

declare(strict_types = 1);

namespace Consistence\Sentry\Factory;

use Consistence\Sentry\Metadata\SentryIdentificator;
use Consistence\Sentry\Type\Sentry;

interface SentryFactory
{

	/**
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $sentryIdentificator
	 * @return \Consistence\Sentry\Type\Sentry
	 * @throws \Consistence\Sentry\Factory\NoSentryForIdentificatorException
	 */
	public function getSentry(SentryIdentificator $sentryIdentificator): Sentry;

}
