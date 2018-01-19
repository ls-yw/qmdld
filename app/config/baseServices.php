<?php
use Phalcon\Session\Adapter\Files as Session;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});


/**
 * Database connection is created based in the parameters defined in the configuration file
 */
 //主库
$di->setShared('dbMaster', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->master->adapter;
    $params = $config->database->master->toArray();

    if ($config->database->master->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});

// Start the session the first time when some component request the session service
$di->setShared(
    "session",
    function () {
        $session = new Session();

        $session->start();

        return $session;
    }
);