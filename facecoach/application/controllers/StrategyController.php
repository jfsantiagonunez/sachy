<?php
class StrategyController extends Zend_Controller_Action
{
	var $model,$modelGoal;
	var $titleView;
	
    public function init()
    {
        /* Load DB_model */

		require_once(APPLICATION_PATH . '/models/TblStrategy.php');
		$this->model = new TblStrategy();	
		
		require_once(APPLICATION_PATH . '/models/TblGoal.php');
		$this->modelGoal = new TblGoal();	
		
		$this->titleView = "Strategy : HOW to achieve your goal";
    }

    public function indexAction()
    {
    	
    	$this->view->titleView = $this->titleView;
    	$this->view->strategies = 'Define HOW you want to achieve your goal';
    		
 		$auth = Zend_Auth::getInstance();
  		if($auth->hasIdentity()) 
 		{
	    	// Query Vision of the user
			$idUser = $auth->getIdentity()->idUser;
        }
    	else
    	{
    		$this->view->NoLoginMessage = 'Login, and define your strategies';
    		return;
    	}
  		
    	$idGoal = $this->getRequest()->getParam('idGoal');
    	
  		if (isset($idGoal) )
  		{
			$this->view->idGoal = $idGoal;
			$this->view->strategies = $this->model->getStrategiesPerGoalKeys('idGoal = ' . $idGoal);
			$this->view->goalTitle = $this->modelGoal->getTitle($idGoal);
  		} 
  		else
  		{		
	  		if ( isset($idUser) )
  			{
  				$error = $this->getStrategiesPerUserKey($idUser);
  				if ($error==0)
  				{
  					$this->view->NoLoginMessage = 'There are no strategies defined';
  				}
  			}
  			else
  			{
  				$this->view->NoLoginMessage = 'Login, and define your strategies';
  				// print nice photo...
  			}
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
		require_once(APPLICATION_PATH . '/forms/Strategy.php');
		$form = new Form_Strategy();
		
		/* load goal id, only set when editing */
		$idGoal = $this->getRequest()->getParam('idGoal');
		$idStrategy = $this->getRequest()->getParam('idStrategy');
		/* if this is a post and it's valid */
		if($this->getRequest()->isPost()){
			try{
				/* determine type and save in the db */
				if($form->isValid($this->getRequest()->getPost() ) )
				{
					$data = $form->getValues();	
					 
					if($type == 'add') 
					{
						$data['idGoal'] = $idGoal;
						$res = $this->model->insert($data);
					}
					else if($type == 'edit')
					{
						$res = $this->model->update($data, $idStrategy);
					}
					
					if(($type == 'add' && $res > 1) || ($type =='edit' && $res == 1)) {
						return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'strategy', 'action' => 'index','idGoal' => $idGoal ),
							'default', true); 
					}else{
						$this->view->error = $res->getMessage();	
					}
				}
				else
				{
					$this->view->error = "Invalid Strategy";
				}
			}
			catch(Exception $e){
				$this->view->error = $e->getMessage();
			}
		}
		/* pass the form to the view */
		if($type == 'edit') $form->populate($this->model->get($idStrategy)->toArray());
		$this->view->form = $form;
	}
    
	public function deleteAction(){

		$idStrategy = $this->getRequest()->getParam('idStrategy');
		$idGoal = $this->getRequest()->getParam('idGoal');
		/* delete user */
		$this->model->delete($idStrategy);
		
		return $this->_helper->redirector->gotoRoute(array(
			'controller' => 'strategy', 'action' => 'index','idGoal' => $idGoal),
			'default', true);
		
		
	}
    
	
	function getStrategiesPerUserKey($idUser)
	{
		require_once(APPLICATION_PATH . '/models/TblGoal.php');
		$modelGoal = new TblGoal();

		$goals = $modelGoal->getGoalsPerUserKey($idUser);
	
		
		if ( $goals->count() > 0)
		{
			$thegoals = '';
			$init = 0;
			foreach ($goals as $goal )
			{
				$goalId = $goal['idGoal'];

				if ($init==0)
				{
					$init=1;
				}
				else
				{
					$thegoals .= ' OR';
				}
				$thegoals .= ' idGoal = ' . $goalId;
								
			}

			$this->view->strategies = $this->model->getStrategiesPerGoalKeys($thegoals);
			return 1;
		}
		else
		{
			return 0;
		}

	}
}
?>