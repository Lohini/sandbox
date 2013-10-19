<?php // vim: ts=4 sw=4 ai:
/**
 * This file is part of Lohini (http://lohini.net)
 *
 * @copyright (c) 2010, 2013 Lopo <lopo@lohini.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 */
namespace App;

use Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route;

/**
 * Description of RouterFactory
 *
 * @author Lopo <lopo@lohini.net>
 */
class RouterFactory
{
	/**
	 * 
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter()
	{
		$router=new RouteList;
		$router[]=new Route('index.php', 'Core:Default:default', Route::ONE_WAY);
		$router[]=new Route('<presenter>[/<action>[/<id>]]', 'Core:Default:default');
		return $router;
	}
}
