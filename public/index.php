<?php

use Ovide\Libs\Mvc\Rest\App;

require __DIR__.'/../vendor/autoload.php';
/**
 * if php<5.6
 */
require __DIR__.'/../vendor/plus/hash_equals.php';

$config = include __DIR__ . "/../app/config/config.php";


$loader = new \Phalcon\Loader();
//Register dirs
$loader->registerDirs(
		array(
			"./../app/controllers",
			"./../app/models"
		)
)->register();
//Create app
$app = App::instance();
//Set up the database service
$app->di->set('db', function(){
	return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
			"host" => "127.0.0.1",
			"username" => "root",
			"password" => "",
			"dbname" => "",
			"options" => array(
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
			)
	));
});

$app->before(function() use ($app) {
	$origin = $app->request->getHeader("ORIGIN") ? $app->request->getHeader("ORIGIN") : '*';

	$app->response->setHeader("Access-Control-Allow-Origin", $origin)
	->setHeader("Access-Control-Allow-Methods", 'GET,PUT,POST,DELETE,OPTIONS')
	->setHeader("Access-Control-Allow-Headers", 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization')
	->setHeader("Access-Control-Allow-Credentials", true);
});

	$app->options('/{catch:(.*)}', function() use ($app) {
		$app->response->setStatusCode(200, "OK")->send();
	});

$app->di->setShared('session', function() {
	$session = new Phalcon\Session\Adapter\Files();
	$session->start();
	return $session;
});
$app->addResources($config->rest->resources);

$app->get("/user/check/{login}/{password}", array(new UsersController(),"checkConnectionAction"));
$app->get("/user/check", array(new UsersController(),"checkConnectedAction"));
$app->post("/user/add", array(new UsersController(),"userAddAction"));
$app->post("/user/connect", array(new UsersController(),"connectAction"));
$app->get("/user/disconnect", array(new UsersController(),"disconnectAction"));
$app->get("/user/exists/{login}", array(new UsersController(),"checkUserExistsAction"));

$app->handle();