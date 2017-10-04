<?php

namespace App;

use Nette;
use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;

class RouterFactory {

    use Nette\StaticClass;

    /**
     * @return Nette\Application\IRouter
     */
    public static function createRouter() {
        $router = new RouteList;
        $router[] = new Route('pokladna/<presenter>/<action>/<id>', array(
            'module' => 'Pokladna',
            'presenter' => 'Prodej',
            'action' => 'default',
            'id' => NULL,
        ));

        $router[] = new Route('<presenter>/<action>/<id>', array(
            'module' => 'Public',
            'presenter' => 'Homepage',
            'action' => 'default',
            'id' => NULL,
        ));
        return $router;
    }

}
