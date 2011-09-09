<?php

	/* entry file, everything is routed through this file */

	/* set up application path */
	define('APPLICATION_PATH', realpath(dirname(__FILE__)
			. '/../application/'));

	/* set up application environment */
	define('APPLICATION_ENVIRONMENT', 'development');

	/* include zend framework in include path */
	set_include_path(APPLICATION_PATH . '/../library/' . PATH_SEPARATOR
			. get_include_path());

	/* set up the autoloader */
	require_once('Zend/Loader/Autoloader.php');
	Zend_Loader_Autoloader::getInstance();

	/* load the bootstrap file */
	require_once('../application/bootstrap.php');

	/* dispatch received request */
	Zend_Controller_Front::getInstance()->dispatch();
?>