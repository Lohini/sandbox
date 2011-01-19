<?php // vim: set ts=4 sw=4 ai:
use Nette\Debug,
	Nette\Environment as NEnvironment,
	BailIff\Environment,
	Nette\Application\Route,
	Nette\Application\SimpleRouter,
	Nette\Web\IHttpResponse,
	BailIff\Network;

// REMOVE THIS LINE
if (!is_file(LIBS_DIR.'/BailIff/loader.php'))
	die('Copy BailIff to /libs/ directory.');

// Step 1: Load BailIff
// this allows load Framework classes automatically so that
// you don't have to litter your code with 'require' statements
require LIBS_DIR.'/BailIff/loader.php';

// Step 2: Configure environment
// 2a) enable Nette\Debug for better exception and error visualisation
Debug::$strictMode=TRUE;
require_once LIBS_DIR.'/BailIff/Utils/Network.php';
$dbg=Network::HostInCIDR($_SERVER['REMOTE_ADDR'], array('10.0.0.0/8', '127.0.0.1')); //must be after config
if (!$dbg && isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	$dbg=Network::HostInCIDR($_SERVER['HTTP_X_FORWARDED_FOR'], array('192.168.0.0/16'));
Debug::enable($dbg? Debug::DEVELOPMENT : Debug::PRODUCTION, VAR_DIR.'/log');
if ($dbg) {
	NEnvironment::setMode(NEnvironment::DEVELOPMENT, TRUE);
	NEnvironment::setMode(NEnvironment::PRODUCTION, FALSE);
	}

// 2b) try to load configuration from config.ini file
try {
	$nconfig=NEnvironment::loadConfig('%appDir%/nette.ini');
	$config=Environment::loadConfig();
	}
catch (\FileNotFoundException $e) {
	NEnvironment::getHttpResponse()->redirect('/install/index.php', IHttpResponse::S307_TEMPORARY_REDIRECT);
	die;
	}

// Step 3: Configure application
$application=Environment::getApplication();
$application->errorPresenter='Error';

// 3b) establish database connection
$application->onStartup[]='BailIff\Database\Connection::initializeAll';

// 3c) load panels

// Step 4: Setup application router
if (NEnvironment::getName()!=='console') {
	$router=$application->getRouter();

	// mod_rewrite detection
		$router[]=new Route('index.php', array(
				'module' => 'Core',
				'presenter' => 'Default',
				'action' => 'default',
				),
			Route::ONE_WAY
			);
		$router[]=new Route('<presenter>/<action>/<id>', array(
				'presenter' => 'Core:Default',
				'action' => 'default',
				'id' => NULL,
				)
			);
	} // if !console

// Step 5: Run the application!
$application->run();
