<?php
namespace Darya\Routing;

use Darya\Http\Request;
use Darya\Http\Response;
use Darya\Service\Contracts\Container;
use Darya\Service\Contracts\ContainerAware;

/**
 * Darya's base controller.
 * 
 * @author Chris Andrew <chris@hexus.io>
 */
abstract class Controller implements ContainerAware {
	
	/**
	 * @var Request
	 */
	public $request;
	
	/**
	 * @var Response
	 */
	public $response;
	
	/**
	 * @var Container
	 */
	public $services;
	
	/**
	 * @var \Darya\View\View
	 */
	public $template;
	
	/**
	 * Instantiate a controller.
	 * 
	 * @param Request  $request
	 * @param Response $response
	 */
	public function __construct(Request $request, Response $response) {
		$this->request = $request;
		$this->response = $response;
	}
	
	/**
	 * Get the URL of the given request, or the controller's current request.
	 * 
	 * @param Request $request [optional]
	 */
	public function url(Request $request = null) {
		$request = $request ?: $this->request;
		
		return $request->route->url();
	}
	
	/**
	 * Set the controller's service container and instantiate an empty view.
	 * 
	 * @param Container $services
	 */
	public function setServiceContainer(Container $services) {
		$this->services = $services;
		
		if ($this->services->has('Darya\View\Resolver')) {
			$this->template = $this->services->resolve('Darya\View\Resolver')->create();
		}
	}
	
}
