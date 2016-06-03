<?php
/**
 * Maes Jerome
 * SlimStrategy.class.php, created at May 29, 2016
 *
 */
namespace system\http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface as InvocationStrategyInterface;


class ControllerStrategy implements InvocationStrategyInterface{
	
	/**
	 * Invoke a route callable with ONLY all route parameters
	 * as individual arguments.
	 *
	 * @param array|callable         $callable
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface      $response
	 * @param array                  $routeArguments
	 *
	 * @return mixed
	 */
	public function __invoke(
			callable $callable,
			ServerRequestInterface $request,
			ResponseInterface $response,
			array $routeArguments
	) {
		
		return call_user_func_array($callable, $routeArguments);
	}
	
}
