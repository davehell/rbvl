<?php

$container = require __DIR__ . '/../app-3-1-2/app/bootstrap.php';

$container->getByType(Nette\Application\Application::class)
	->run();
