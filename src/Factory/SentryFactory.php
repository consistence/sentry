<?php

namespace Consistence\Sentry\Factory;

use Consistence\Sentry\Metadata\SentryIdentificator;

interface SentryFactory
{

	/**
	 * @param \Consistence\Sentry\Metadata\SentryIdentificator $sentryIdentificator
	 * @return \Consistence\Sentry\Type\Sentry
	 * @throws \Consistence\Sentry\Factory\NoSentryForIdentificatorException
	 */
	public function getSentry(SentryIdentificator $sentryIdentificator);

}
