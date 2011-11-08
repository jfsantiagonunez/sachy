<?php
require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class WebappController extends BaseController
{
	var $model;
	var $titleView;
	
	protected $_idkey = 'notodefine';
	protected $_controllername = 'catalogue';
	
    public function init()
    {
		require_once(APPLICATION_PATH . '/models/ModelWine.php');
		$this->model = new ModelWine();
		Zend_Layout::getMvcInstance()->setLayout("smartphone");
    }
    
    
    public function indexAction()
    {
      	$auth       = Zend_Auth::getInstance(); 
    	if(!$auth->hasIdentity()){
      		$this->_redirect('/user/index');
    	}else{
    	  	
    	  	// Do nothing.
    	}
    }
    
    
    public function grapeAction()
    {
    	if($this->getRequest()->isPost())
    	{
    		$data = $this->getRequest()->getPost();
    		if ( isset($data['searchgrape']))
    		{
    			return $this->_helper->redirector->gotoRoute(array(
    					'controller' => 'catalogue' , 'action' => 'grape', 'searchwine' => $data['searchgrape'] ),
    					'default', true);
    
    		}else{
    			return $this->_helper->redirector->gotoRoute(array(
        									'controller' => 'catalogue' , 'action' => 'grape' ),
        									'default', true);
    		}
    	}
    	else
    	{
    		$searchwine = $this->getRequest()->getParam('searchrgrape');
    
    		$this->view->grapes = $this->model->queryGrapes($searchgrape);
    		 
    	}
    
    }
    
    public function choosewineAction()
    {
 		if ($this->getRequest()->isPost())
 		{
 			$data = $this->getRequest()->getPost();
 			$this->view->wines = $this->model->chooseWine($data);
 		}else {
 			$this->view->query='1';
   			require_once(APPLICATION_PATH . '/forms/chosewine.php');
    		$this->view->form = new Form_ChooseWine();
 		}
    }
	

    
    
    
}