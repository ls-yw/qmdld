<?php

$router = $di->getRouter();

// Define your routes here

$modules = $config->modules->toArray();
$router->add('/('.implode('|', array_keys($modules)).')/?([\w]*)/?([\w]*)(/.*)*',['module'=>1,'controller' => 2,'action' => 3,'params'=>4]);
$router->setDefaultModule('index');
$router->handle();
