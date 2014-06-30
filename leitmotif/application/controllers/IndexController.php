<?php

class IndexController extends Zend_Controller_Action
{
	var $model;
	
    public function init()
    {
    	require_once(APPLICATION_PATH . '/models/ModelLeitmotif.php');
    	$this->model = new ModelLeitmotif();
    	   
    }

    public function indexAction()
    {
      	$auth   = Zend_Auth::getInstance(); 
    	if(!$auth->hasIdentity()){
      		$this->_redirect('/user/index');
    	}
    }

}
?>