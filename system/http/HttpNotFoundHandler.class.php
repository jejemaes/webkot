<?php
/**
 * Maes Jerome
 * HttpNotFoundHandler.class.php, created at Jun 3, 2016
 *
 */
namespace system\http;
use Slim\Handlers\NotFound;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Body;
use UnexpectedValueException;


class HttpNotFoundHandler extends NotFound {
	
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response) {
		// TODO customize it !
		return parent::__invoke($request, $response);
	}
	
}