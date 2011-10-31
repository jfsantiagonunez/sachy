<?php
	/* keep session alive for 10 hours */
	ini_set('session.gc_maxlifetime', 36000);

	/* get front controller instance */
	$frontController = Zend_Controller_Front::getInstance();

	/* set controller directory */
	$frontController->setControllerDirectory('../application/controllers/');

	/* set environment to development */
	$frontController->setParam('env', APPLICATION_ENVIRONMENT);

	/* start layout manager */
	Zend_Layout::startMvc(APPLICATION_PATH . '/layouts/scripts/');

	/* set doctype, needed for automatic forms etc. */
	$view = Zend_Layout::getMvcInstance()->getView();
	$view->doctype('XHTML11');
	$view->addHelperPath(APPLICATION_PATH . '/../library/Ivaldi/View/Helper/',
			'Ivaldi_View_Helper');
	$view->addHelperPath(APPLICATION_PATH . '/../library/Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');

	$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer(); 
	$viewRenderer->setView($view); 
	Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
	
	/* load configuration from file */
	$configuration = new Zend_Config_Ini(APPLICATION_PATH
			. '/configs/application.ini', APPLICATION_ENVIRONMENT);

	/* load database configuration */
	$dbAdapter = Zend_Db::factory($configuration->database,
			$configuration->database);

	/* set as default database adapter */
	Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);

	/* load registery and add configuration to it */
	$registry = Zend_Registry::getInstance();
	$registry->configuration = $configuration;
	$registery->dbAdapter = $dbAdapter;




	/* clean up global vars */
	unset($frontController, $view, $viewRenderer, $configuration, $dbAdapter,
			$registry, $translate);
