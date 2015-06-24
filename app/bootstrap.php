<?php
// Step 1: Load Nette Framework
// this allows load Nette Framework classes automatically so that
// you don't have to litter your code with 'require' statements
// require LIBS_DIR . '/Nette/loader.php';
require_once LIBS_DIR . '/Nette/loader.php';



// Step 2: Configure environment
// 2a) enable Nette\Debug for better exception and error visualisation
//Debug::enable('147.229.208.189');
//Debug::$strictMode = true;
Debug::enable();
//Debug::enable(NULL, TRUE, "david.hellebrand@seznam.cz");
//Debug::enable(Debug::PRODUCTION);
//Debug::enable(Debug::DEVELOPMENT);

// 2b) load configuration from config.ini file
Environment::loadConfig();

// 2c) check if directory /app/temp is writable
if (@file_put_contents(Environment::expand('%tempDir%/_check'), '') === FALSE) {
	throw new Exception("Make directory '" . Environment::getVariable('tempDir') . "' writable!");
}

// 2d) enable RobotLoader - this allows load all classes automatically
$loader = new RobotLoader();
$loader->addDirectory(APP_DIR);
$loader->addDirectory(LIBS_DIR);
$loader->register();
$loader->rebuild();


// Step 3: Configure application
// 3a) get and setup a front controller
$application = Environment::getApplication();

// 3b) establish database connection
$application->onStartup[] = 'Database::initialize';
$application->errorPresenter = 'Error';
$application->catchExceptions = false; //false = ladenka, true = server error



// Step 4: Setup application router
$router = $application->getRouter();

// mod_rewrite detection
if (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) {

  Route::setStyleProperty('presenter', Route::FILTER_TABLE, array(
    'ke-stazeni' => 'Download',
    'turnaje-akce' => 'Akce',
  ));

  Route::setStyleProperty('action', Route::FILTER_TABLE, array(
    'pridat' => 'add',
    'upravit' => 'edit',
    'smazat' => 'delete',
  ));


 $router[] = new Route('index.php', array(
		'presenter' => 'Default',
		'action' => 'default',
	), Route::ONE_WAY);


	$router[] = new Route('<presenter>/<action>/<id>', array(
		'presenter' => 'Default',
		'action' => 'default',
		'id' => NULL,
	));


} else {
	$router[] = new SimpleRouter('Default:default');
}



// Step 5: Run the application!
$application->run();
