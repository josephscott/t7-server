<?php
declare( strict_types = 1 );

namespace T7;

use function is_callable;
use function is_readable;
use function is_string;
use function substr;

class Router {
	public function __construct() {}

	public static function run( string|callable $handler, array $vars ) {
		if ( \is_callable( $handler ) ) {
			$handler( $vars );
		}

		$call_file = function ( string $__file, array $vars ) {
			require $__file;
		};

		if (
			\is_string( $handler )
			&& substr( $handler, 0, 1 ) === '/'
			&& is_readable( $handler )
		) {
			$call_file( $handler, $vars );
		}

		exit();
	}
}
