<?php // vim: ts=4 sw=4 ai:

// uncomment this line if you must temporarily take down your site for maintenance
// require '.maintenance.php';

// the identification of this site
define('SITE', 'Lohini skeleton');
// absolute filesystem path to the web root
define('WWW_DIR', __DIR__);
// absolute filesystem path to the root
define('ROOT_DIR', realpath(WWW_DIR.'/..'));
// absolute filesystem path to the application root
define('APP_DIR', ROOT_DIR.'/app');
// absolute filesystem path to the libraries
define('LIBS_DIR', ROOT_DIR.'/libs');
// absolute filesystem path to variables
define('VAR_DIR', ROOT_DIR.'/var');
define('TEMP_DIR', VAR_DIR.'/temp');
// load bootstrap file
require APP_DIR.'/bootstrap.php';
