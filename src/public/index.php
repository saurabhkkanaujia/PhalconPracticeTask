<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Escaper;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream as ls;


use Phalcon\Di;
use Phalcon\Session\Manager;
use Phalcon\Session\Adapter\Stream;

use Phalcon\Http\Response\Cookies;
use Phalcon\Http\Response;

$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);

$loader->registerNamespaces(
    [
        'App\Components' => APP_PATH . '/component'
    ]
);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$application = new Application($container);



$container->set(
    'db',
    function () {
        return new Mysql(
            [
                'host'     => 'mysql-server',
                'username' => 'root',
                'password' => 'secret',
                'dbname'   => 'practice',
            ]
        );
    }
);

// $container->set(
//     'mongo',
//     function () {
//         $mongo = new MongoClient();

//         return $mongo->selectDB('phalt');
//     },
//     true
// );

$container->set(
    'session',
    function () {
        $session = new Manager();
        $files = new Stream(
            [
                'savePath' => '/tmp',
            ]
        );

        $session
            ->setAdapter($files)
            ->start();

        return $session;
    }
);

$container->set(
    'time',
    function () {
        return date("D-M-Y, h:m:s");
    }
);

$container->set(
    'error',
    function () {
        $response = new Response();

        $response->setStatusCode(403, 'Not Found');

        $response->setContent("Authentication Failed");

        $response->send();
    }
);

$container->set(
    'escaper',
    function () {
        return new Escaper();
    }
);

$container->set(
    'cookies',
    function () {
        $cookies = new Cookies();
        $cookies->useEncryption(false);
        return $cookies;
    }
);

$container->set(
    'login_logger',
    function () {
        $adapter = new ls('../app/logs/login.log');
        $logger  = new Logger(
            'messages',
            [
                'login' => $adapter,
            ]
        );
        return $logger;
    }
);

$container->set(
    'signup_logger',
    function () {
        $adapter = new ls('../app/logs/signup.log');
        $logger  = new Logger(
            'messages',
            [
                'signup' => $adapter,
            ]
        );
        return $logger;
    }
);


try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}
