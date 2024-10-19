<?php
declare( strict_types = 1 );

namespace T7;

use function is_callable;
use function is_readable;
use function is_string;
use function substr;

class Router {
	private string $handler;
	private array $get;

	public function __construct(
		string $handler,
		array $get
	) {
		$this->handler = $handler;
		$this->get = $get;
	}

	public function run() {
		$call_file = function( string $__file, array $get ) {
			require $__file;
		};

/*
		if ( is_callable( $this->handler ) ) {
			return $handler( $this->get );
		}
 */
		if (
			is_string( $this->handler )
			&& substr( $this->handler, 0, 1 ) === '/'
			&& is_readable( $this->handler )
		) {
			$call_file( $this->handler, $this->get );
		}
	}
}
