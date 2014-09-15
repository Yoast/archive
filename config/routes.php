<?php

/**
 * Framework config file
 *
 * Routes.php - define a route
 */

global $framework_routes;

$framework_routes->add_route( true,
	'hello_index',
	array(
	'controller'	=>	'hello',
	'action'		=>	'index'
) );

?>