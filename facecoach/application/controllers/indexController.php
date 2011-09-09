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
    	return $this->_helper->redirector->gotoRoute(array('controller' => 'vision', 'action' => 'index'),'default', true);
    }
    
    
    public function loginAction()
    {
    	$request = $this->getRequest();  
    	$auth       = Zend_Auth::getInstance(); 
    	if(!$auth->hasIdentity()){
      		$this->_redirect('/user/loginform');
    	}else{
    	  	$this->_redirect('/user/userpage');
    	}
    }
}

?>