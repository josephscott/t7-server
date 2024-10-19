<?php
declare( strict_types = 1 );

require __DIR__ . '/../vendor/autoload.php';

$server = new T7\Server(
	routes: [
		[ 'GET', '/', __DIR__ . '/home.php' ],
		[ 'GET', '/hello/[{name}/]', __DIR__ . '/hello.php' ],
	]
);

error_log( __FILE__ );

$server->start();
