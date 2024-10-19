<?php
declare( strict_types = 1 );

namespace T7;

use function error_log;
use function header;
use function is_callable;
use function is_readable;
use function is_string;
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
				// Redirect no trailing slashes
				if ( substr( $uri, -1 ) !== '/' ) {
					$qs = '';
					if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
						$qs = '?' . $_SERVER['QUERY_STRING'];
					}
					header( 'Location: ' . $uri . '/' . $qs, true, 301 );
					exit();
				}
				// Do 404
				break;
			case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
				error_log( "T7\Server: method not allowed, $http_method" );
				$allowed_methods = $route_info[1];
				break;
			case \FastRoute\Dispatcher::FOUND:
				$router = Router::run(
					handler: $route_info[1],
					vars: $route_info[2]
				);
				break;
		}
	}
}
