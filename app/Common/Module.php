<?php

namespace EP\Common;

use Phalcon\Db\Adapter\Pdo\Mysql as Db;
use Phalcon\DI\InjectionAwareInterface;
use Phalcon\Loader;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Mvc\Url;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\View;
use Phalcon\Session\Adapter\Files as Session;

class Module implements ModuleDefinitionInterface, InjectionAwareInterface {

	/**
	 * Phalcon object.
	 * @var \Phalcon\DI
	 */
	protected $di;

	public function registerAutoloaders() {
		$loader = new Loader();
		$loader->registerNamespaces(require APPPATH . '/Config/namespaces.php');
		$loader->register();
	}

	/**
	 * @param \Phalcon\DiInterface $di
	 */
	public function registerServices($di) {
		$di->set('dispatcher', function () {
			$dispatcher = new Dispatcher();
			$dispatcher->setDefaultNamespace('EP\Administration\Controllers');
			return $dispatcher;
		});

		// set volt template engine options
		$di->setShared('volt', function ($view, $di) {
			$volt = new Volt($view, $di);
			$volt->setOptions(array(
				'compiledPath' => APPPATH . '/ViewsCache/',
				'compiledSeparator' => '.'
			));
			return $volt;
		});

		// template engine
		$di->setShared('view', function () {
			$view = new View();
			$view->setViewsDir(APPPATH . '/Views/');
			$view->registerEngines(array(
				'.volt' => 'volt'
			));
			return $view;
		});

		// set base url
		$di->setShared('url', function () {
			$url = new Url();
			$url->setBaseUri(BASE_URL);
		});

		// init session
		$di->setShared('session', function () {
			$session = new Session();
			$session->start();
			return $session;
		});

		// set database connection params
		$di->set('db', function () use ($di) {
			$db = new Db(array(
				'adapter'  => 'Mysql',
				'host'     => '192.168.56.101',
				'port'     => '3306',
				'username' => 'root',
				'password' => '123123',
				'dbname'   => 'coderuntime',
				'charset'  => 'utf8'
			));
			return $db;
		});
	}

	/**
	 * Injects Phalcon Dependency Injector into this object.
	 * @param \Phalcon\DiInterface $di
	 */
	public function setDI($di) {
		$this->di = $di;
	}

	/**
	 * Obtain the Phalcon Dependency Injector object.
	 * @return \Phalcon\DI|\Phalcon\DiInterface
	 */
	public function getDI() {
		return $this->di;
	}
} 