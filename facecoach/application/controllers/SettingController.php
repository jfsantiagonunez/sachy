<?php
class SettingController extends Zend_Controller_Action
{
	var $titleView;	
    public function init()
    {
		$this->titleView='Settings';
    }

    public function indexAction()
    {
   	
    	$auth = Zend_Auth::getInstance();
    	if($auth->hasIdentity()) 
    	{
    		// Query Vision of the user
			$idUser = $auth->getIdentity()->idUser;
			$this->view->idUser = $idUser;
    	}
    	else
    	{
    		return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'user', 'action' => 'index'),
							'default', true );
    	}
    }

    
    
}
?>