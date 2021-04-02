<?php

$container = require __DIR__ . '/../app-3-0-8/app/bootstrap.php';

$container->getByType(Nette\Application\Application::class)
	->run();
