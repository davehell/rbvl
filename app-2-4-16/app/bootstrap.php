<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

// if (strpos(getenv("DOCUMENT_ROOT"), "D:/wamp64/www") !== false) {
//     $configurator->setDebugMode(TRUE);
// }
// else {
//     $configurator->setDebugMode(FALSE); 
// }
$configurator->setDebugMode(TRUE);

$configurator->enableTracy(__DIR__ . '/../log');

$configurator->setTimeZone('Europe/Prague');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

return $container;