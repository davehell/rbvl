<?php

namespace App;

use Nette;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	/**
	 * @return Nette\Application\IRouter
	 */
	public static function createRouter()
	{
		$router = new RouteList;
		$router[] = new Route('index.php', 'Default:default', Route::ONE_WAY);
		$router[] = new Route('<presenter>/<action>[/<id>]', array(
		    'presenter' => array(
		        Route::VALUE => 'Default',
		        Route::FILTER_TABLE => array(
		            // řetězec v URL => presenter
		            'ke-stazeni' => 'Download',
		            'turnaje-akce' => 'Akce'
		        ),
		    ),
		    'action' => array(
		        Route::VALUE => 'default',
		        Route::FILTER_TABLE => array(
		            'pridat' => 'add',
		            'upravit' => 'edit',
		            'smazat' => 'delete',
		        ),
		    ),
		    'id' => NULL,
		));
		return $router;
	}
}
