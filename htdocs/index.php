<?php // vim: ts=4 sw=4 ai:
/**
 * This file is part of Lohini (http://lohini.net)
 *
 * @copyright (c) 2010, 2013 Lopo <lopo@lohini.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 */

define('WWW_DIR', __DIR__);
define('ROOT_DIR', realpath(WWW_DIR.'/..'));
define('APP_DIR', ROOT_DIR.'/app');

// uncomment this line if you must temporarily take down your site for maintenance
// require '.maintenance.php';

require_once ROOT_DIR.'/app/bootstrap.php';
