<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$moduleNamespaces = [
        'Basic'            => $config->application->basicDir,
        'Library'          => $config->application->libraryDir,
        'Models'           => $config->application->modelsDir,
        'Services'         => $config->application->servicesDir,
    ];

$loader->registerNamespaces($moduleNamespaces);
$loader->register();
