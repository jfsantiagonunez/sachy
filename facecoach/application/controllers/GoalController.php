<?php
class GoalController extends Zend_Controller_Action
{
	var $model;
	var $titleView;
	
    public function init()
    {
        /* Load DB_model */

		require_once(APPLICATION_PATH . '/models/TblGoal.php');
		$this->model = new TblGoal();	
		require_once(APPLICATION_PATH . '/models/TblVision.php');
		$this->modelVision = new TblVision();	
		$this->titleView = "Goals : WHAT you want to achieve";
    }

    public function indexAction()
    {
    	$this->view->goals = 'Define WHAT you want to achieve';
    	$this->view->titleView = $this->titleView;
    	
    	
    	$auth = Zend_Auth::getInstance();
    	if($auth->hasIdentity()) 
    	{
    		// Query Vision of the user
			$idUser = $auth->getIdentity()->idUser;
			$this->view->idUser = $idUser;
			$this->view->goals = $this->model->getGoalsPerUserKey($idUser);
			$this->view->visionTitle = $this->modelVision->getVisionTitlePerUserKey($idUser);
    	}
    	else
    	{
    		$this->view->NoLoginMessage = 'Login, and define your goals';
    	}
    }
    
    public function addAction(){
		$this->saveForm('add');		
	}
	
	public function editAction() {
		$this->saveForm('edit');
	}
	
	public function saveForm($type){
		$this->view->titleView = $this->titleView;
		require_once(APPLICATION_PATH . '/forms/Goal.php');
		$form = new Form_Goal();
		
		/* load goal id, only set when editing */
		$idGoal = $this->getRequest()->getParam('idGoal');
		$idUser = $this->getRequest()->getParam('idUser');
		/* if this is a post and it's valid */
		if($this->getRequest()->isPost()){
			try{
				/* determine type and save in the db */
				if($form->isValid($this->getRequest()->getPost() ) )
				{
					$data = $form->getValues();	
					 
					if($type == 'add') 
					{
						$data['idUser'] = $idUser;
						$res = $this->model->insert($data);
					}
					else if($type == 'edit')
					{
						$res = $this->model->update($data, $idGoal);
					}
					
					if(($type == 'add' && $res > 1) || ($type =='edit' && $res == 1)) {
						return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'goal', 'action' => 'index'),
							'default', true); 
					}else{
						$this->view->error = $res->getMessage();	
					}
				}
				else
				{
					$this->view->error = "Invalid Goal";
				}
			}
			catch(Exception $e){
				$this->view->error = $e->getMessage();
			}
		}
		/* pass the form to the view */
		if($type == 'edit') $form->populate($this->model->get($idGoal)->toArray());
		$this->view->form = $form;
	}
    
	public function deleteAction(){
		/* get user id*/
		$idGoal = $this->getRequest()->getParam('idGoal');
		
		/* delete user */
		$this->model->delete($idGoal);
		
		return $this->_helper->redirector->gotoRoute(array(
			'controller' => 'goal', 'action' => 'index'),
			'default', true);
		
		
	}
    
}
?>