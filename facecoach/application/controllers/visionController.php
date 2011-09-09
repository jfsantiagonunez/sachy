<?php
class VisionController extends Zend_Controller_Action
{
	var $model;
	var $titleView;
	
    public function init()
    {
        /* Load DB_model */

		require_once(APPLICATION_PATH . '/models/TblVision.php');
		$this->model = new TblVision();
		$this->titleView = 	"Vision : WHO you want to become";
    }

    public function indexAction()
    {
    	$this->view->titleView = $this->titleView;
    	$auth = Zend_Auth::getInstance();
    	if($auth->hasIdentity()) 
    	{
    		// Query Vision of the user
			$idUser = $auth->getIdentity()->idUser;
			
			if (isset($idUser))
			{
			
				$this->view->vision = $this->model->getVisionTitlePerUserKey($idUser);

				$this->view->idUser = $idUser;
			
   			}
			else
			{
				$auth->clearIdentity();
				$this->view->vision = 'Define WHO you want to become';
			}
    	}
    	else
    	{

    		$this->view->vision = 'Define WHO you want to become';
    		//echo "<img src=\"img/vision.png\" />";
    	}
    }
    
	public function editAction() {
	
		$this->view->titleView = $this->titleView;
		require_once(APPLICATION_PATH . '/forms/Vision.php');
		$form = new Form_Vision();
		
		$idUser = $this->getRequest()->getParam('idUser');
		$result = $this->model->getVisionForUserId($idUser);
		$type = 'edit';
		if ( $result != null )
		{
			$type = 'edit';
		}
		else 
		{
			$type = 'add';
		}
		
		
		/* if this is a post and it's valid */
		if($this->getRequest()->isPost()){
			try{
				/* determine type and save in the db */
				if($form->isValid($this->getRequest()->getPost() ) )
				{
					$data = $form->getValues();	
					 
					
					if ( $type == 'add' )
					{
						$data['idUser'] = $idUser;
						$res = $this->model->insert($data);

					}
					elseif ( $type == 'edit' )
					{
						$res = $this->model->updateVisionForUserKey($data, $idUser);
					}
					
					if(($type == 'add' && $res > 1) || ($type =='edit' && $res == 1)) {
						return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'vision', 'action' => 'index'),
							'default', true); 
					}else{
						$this->view->error = $res->getMessage();	
					}
				}
				else
				{
					$this->view->error = "Invalid Vision";
				}
			}
			catch(Exception $e){
				$this->view->error = $e->getMessage();
			}
		}
		/* pass the form to the view */
		if($type == 'edit')
		{
		 	$form->populate($this->model->getVisionForUserId($idUser)->toArray());
		}
		$this->view->form = $form;
	}
}
?>