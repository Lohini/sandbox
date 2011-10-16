<?php // vim: ts=4 sw=4 ai:
use Nette\Environment,
	Nette\Application\Routers\Route,
	Nette\Http\IResponse,
	Nette\Diagnostics\Debugger,
	Lohini\Utils\Network;

// REMOVE THIS LINE
if (!is_file(LIBS_DIR.'/Lohini/loader.php')) {
	die('Copy Lohini to directory '"LIBS_DIR."'.');
	}
if (file_exists(WWW_DIR.'/install')) {
	die("Remove installation dir '".WWW_DIR."/install'");
	}

// Step 1: Load Lohini
// this allows load Framework classes automatically so that
// you don't have to litter your code with 'require' statements
require LIBS_DIR.'/Lohini/loader.php';

// Step 2: Configure environment
// 2a) enable Nette\Debug for better exception and error visualisation
$dbg=Network::HostInCIDR(Network::getRemoteIP(), array('10.0.0.0/8', '127.0.0.1'));
Debugger::enable($dbg? Debugger::DEVELOPMENT : Debugger::PRODUCTION, VAR_DIR.'/log');

// 2b) try to load configuration from config.neon file
try {
	$section=
		isset($_SERVER['APPENV'])
			? $_SERVER['APPENV']
			: ($dbg
				? Environment::DEVELOPMENT
				: Environment::PRODUCTION
				)
		;
	$configurator=new \Lohini\DI\Configurator;
	$configurator->loadConfig(
		array(
			'%appDir%/nette.neon',
			'%appDir%/lohini.neon',
			'%appDir%/config.neon'
			),
		$section
		);
	}
catch (\Nette\FileNotFoundException $e) {
	Environment::getHttpResponse()->redirect('/install/index.php', IHttpResponse::S307_TEMPORARY_REDIRECT);
	die;
	}
if ($dbg) {
	$configurator->container->params['productionMode']=FALSE;
	}

// Step 3: Configure application
$application=$configurator->container->application;
$application->errorPresenter='Error';

// 3a) load panels
\Lohini\Database\Doctrine\ORM\Diagnostics\Panel::register();

// Step 4: Setup application router
$application->onStartup[]=function() use ($application) {
	$router=$application->getRouter();

	$router[]=new Route('index.php', array(
			'lang' => Environment::getVariable('lang', 'en'),
			'module' => 'Core',
			'presenter' => 'Default',
			'action' => 'default',
			),
		Route::ONE_WAY
		);
	$router[]=new Route('[<lang=en [a-z]{2}>/]<presenter>[/<action>[/<id>]]', 'Core:Default:default');
	}

// Step 5: Run the application!
$application->run();
