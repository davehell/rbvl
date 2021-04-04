<?php

namespace App;

use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;

final class RouterFactory
{
	/**
	 * @return Nette\Application\IRouter
	 */
    public static function createRouter(): RouteList
    {
        $router = new RouteList;
        $router->addRoute('index.php', 'Default:detail', $router::ONE_WAY);
        $router->addRoute('<presenter>/<action>[/<id>]', [
            'presenter' => [
                Route::VALUE => 'Default',
                Route::FILTER_TABLE => [
                    // řetězec v URL => presenter
                    'ke-stazeni' => 'Download',
                    'turnaje-akce' => 'Akce'
                ],
            ],
            'action' => [
                Route::VALUE => 'default',
                Route::FILTER_TABLE => [
                    'pridat' => 'add',
                    'upravit' => 'edit',
                    'smazat' => 'delete'
                ],
            ],
            'id' => NULL
        ]);

        return $router;
    }
}
