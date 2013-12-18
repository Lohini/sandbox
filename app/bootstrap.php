<?php // vim: ts=4 sw=4 ai:
/**
 * This file is part of Lohini (http://lohini.net)
 *
 * @copyright (c) 2010, 2013 Lopo <lopo@lohini.net>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License Version 3
 */
require_once ROOT_DIR.'/vendor/autoload.php';

// Configure application
$configurator=new \Lohini\Configurator;
// Enable Nette Debugger for error visualisation & logging
$configurator->enableDebugger(ROOT_DIR.'/log');
// Enable RobotLoader - this will load all classes automatically
$configurator->setTempDirectory(ROOT_DIR.'/temp');
$robot=$configurator->createRobotLoader()
		->addDirectory(APP_DIR)
		->register();

// Create Dependency Injection container from config.neon file
$configurator->addConfig(APP_DIR.'/config/config.neon');
if (file_exists($localConf=APP_DIR.'/config/config.local.neon')) {
	$configurator->addConfig($localConf, $configurator::NONE);
	}

/** @var SystemContainer $container */
$container=$configurator->createContainer();

$container->getService('application')->run();
