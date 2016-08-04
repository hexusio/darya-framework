<?php
namespace Darya\Service\Contracts;

use Darya\Service\Contracts\Container;

interface Provider
{
	/**
	 * Register services with the given service container.
	 * 
	 * @param Container $services
	 */
	public function register(Container $services);
}
