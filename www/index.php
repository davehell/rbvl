<?php

$container = require __DIR__ . '/../app-2-4-16/app/bootstrap.php';

$container->getByType(Nette\Application\Application::class)
	->run();
