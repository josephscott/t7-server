<?php
declare( strict_types = 1 );

namespace T7;

use function header;
use function rawurldecode;
use function rtrim;
use function strpos;
use function substr;

class Server {
	private array $routes = [];

	public function __construct(
		array $routes,
	) {
		$this->routes = $routes;
	}

	public function start() : void {
		$dispatcher = \FastRoute\cachedDispatcher(
			function ( \FastRoute\RouteCollector $r ) {
				foreach( $this->routes as $s ) {
					// Array:
					// [0] - method
					// [1] - path/pattern
					// [2] - handler
					$r->addRoute( $s[0], $s[1], $s[2] );
				}
			},
			[
				'cacheFile' => '/dev/null',
				'cacheDisabled' => true,
			]
		);

		$http_method = $_SERVER['REQUEST_METHOD'] ?? '';
		$uri = $_SERVER['REQUEST_URI'] ?? '';
		$pos = strpos( $uri, '?' );
		if ( $pos !== false ) {
			$uri = substr( $uri, 0, $pos );
		}

		$uri = rawurldecode( $uri );
		$route_info = $dispatcher->dispatch( $http_method, $uri );

		// Route info
		// [0] - status
		// [1] - handle/allowed methods
		// [2] - route vars
		switch ( $route_info[0] ) {
			case \FastRoute\Dispatcher::NOT_FOUND:
				// Redirect trailing slashes
				if ( substr( $uri, -1 ) === '/' ) {
					header( 'Location: ' . rtrim( $uri, '/' ), true, 301 );
					exit();
				}
				break;
			case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
				$allowed_methods = $route_info[1];
				break;
			case \FastRoute\Dispatcher::FOUND:
				$handler = $route_info[1];
				$vars = $route_info[2];
				break;
		}
	}
}
