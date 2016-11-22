<?php
include_once('lib/Config.php');
$config = new Config();
$ROUTE_CONFIGS = $config->getRouteConfig();
function getCurrentUri()
{
	$basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
	$uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
	if (strstr($uri, '?'))
	{
		$uri = substr($uri, 0, strpos($uri, '?'));
	};
	$uri = '/' . trim($uri, '/');
	return $uri;
}

/**
 * Gets uri of route in Route Configs array.
 *
 * @param $routes
 * @param $config
 * @return string
 */
function parseRoute($routes, $config){
	$route = $routes[0];
	if(!($current_route_config = $config[$route])){ return 'Route Does Not Exist';}
	for($i = 1; $i < count($routes);$i++){
		$route = $routes[$i];
		if(array_key_exists('sub_route',$current_route_config) && array_key_exists($route,$current_route_config['sub_route']))
		{
			$current_route_config = $current_route_config['sub_route'][$route];
		}
		else
		{
			return 'Route Does Not Exist';
		}
	}
	return $current_route_config;
}

$base_url = getCurrentUri();
$routes = array();
$url_routes = explode('/', $base_url);
foreach($url_routes as $route)
{
	if(trim($route) != '')
	{
		array_push($routes, $route);
	}
}
if(!empty($routes)) {
	$route = parseRoute($routes, $ROUTE_CONFIGS);
}
else
{
	$route = 'Route Does Not Exist';
}
if(!empty($routes) && $route != 'Route Does Not Exist')
{
	if(isset($route['action']))
	{
		if(isset($route['action']['rest_class']) && isset($route['action']['rest_file'])) {
			set_include_path('/RestApi/');
			include $route['uri'] . $route['action']['rest_file'];
			/** @var REST $api */
			$api = new $route['action']['rest_class'];
			header('Content-Type: application/json');
			echo $api->processRequest();
		}
		else if(isset($route['action']['view']))
		{
			include 'lib/ViewRenderer.php';
			$viewRenderer = new ViewRenderer();
			$viewRenderer->renderView($route['action']['view']);
		}
	}
	else
	{
		include $route['uri'];
	}
}
else
{
	include 'lib/ViewRenderer.php';
	$viewRenderer = new ViewRenderer();
	$viewRenderer->renderView('index.phtml');
}
?>