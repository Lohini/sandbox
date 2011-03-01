<?php // vim: set ts=4 sw=4 ai:
// the identification of this site
define('SITE', 'BailIff skeleton');
// absolute filesystem path to the web root
define('WWW_DIR', __DIR__);
// absolute filesystem path to the application root
define('APP_DIR', realpath(WWW_DIR.'/../app'));
// absolute filesystem path to the libraries
define('LIBS_DIR', realpath(WWW_DIR.'/../libs'));
// absolute filesystem path to variables
define('VAR_DIR', realpath(WWW_DIR.'/../var'));
define('TEMP_DIR', realpath(VAR_DIR.'/temp'));
// load bootstrap file
require APP_DIR.'/bootstrap.php';
