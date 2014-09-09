<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Application as PhalconApp;

/**
 * Entry point for application
 */
define('ENVIRONMENT', ! empty($_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : 'development');
define('DEBUG', ! empty($_SERVER['DEBUG']));
define('BASE_URL', 'http://' . (ENVIRONMENT === 'production' ? 'www' : 'dev') . '.coderuntime.com');
if (DEBUG) {
	$debug = new Debug();
	$debug->listen();
}

// Show errors?
switch (ENVIRONMENT) {
	case 'production':
		error_reporting(0);
		break;
	case 'development':
	default:
		error_reporting(E_ALL);
}
// path to Common app/module that contains common libraries/models/configs.
define('COMMON', '../app/Common');
// the only application for now.
$app = 'Administration';
define('APPPATH', '../app/' . $app);


// set the timezone, to avoid server dependencies
date_default_timezone_set('UTC');

$di = new FactoryDefault();

$di->set('router', function () use ($app) {
	$router = new Router();
	$router->setDefaultModule($app);
	return $router;
});

/**
 * Initiate the request
 */
$application = new PhalconApp($di);
$application->registerModules(require COMMON . '/Config/modules.php');
echo $application->handle()->getContent();