<?php

class IndexController extends Zend_Controller_Action
{
	var $model;
	
    public function init()
    {
        /* Load DB_model */

    }
    
    public function indexAction()
    {
   		$auth       = Zend_Auth::getInstance(); 
    	if(!$auth->hasIdentity()){
      		$this->_redirect('/user/index');
    	}else{
    	  	$this->_redirect('/catalogue/choosewine');
    	}
    }
    
    
    public function loginAction()
    {
    	$request = $this->getRequest();  
    	$auth       = Zend_Auth::getInstance(); 
    	if(!$auth->hasIdentity()){
      		$this->_redirect('/user/index');
    	}else{
    	  	$this->_redirect('/catalogue/choosewine');
    	}
    }
}

?>