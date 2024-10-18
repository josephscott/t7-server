<?php
declare( strict_types = 1 );

namespace T7;

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
				foreach( $this->routes as $method => $route ) {
					foreach( $route as $details ) {
						$r->addRoute( $method, $details['pattern'], $details['handler'] );
					}
				}
			},
			[
				'cacheFile' => '/dev/null',
				'cacheDisabled' => true,
			]
		);
	}
}
