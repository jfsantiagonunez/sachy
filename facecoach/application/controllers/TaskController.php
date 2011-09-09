<?php
class TaskController extends Zend_Controller_Action
{
	var $model;
	var $titleView;
	var $modelGoal;
	var $modelStrategy;

    public function init()
    {
        /* Load DB_model */

		require_once(APPLICATION_PATH . '/models/TblTask.php');
		$this->model = new TblTask();	
		$this->titleView = "Task :  WHAT needs to be done";
		require_once(APPLICATION_PATH . '/models/TblGoal.php');
		$this->modelGoal = new TblGoal();
		require_once(APPLICATION_PATH . '/models/TblStrategy.php');
		$this->modelStrategy = new TblStrategy();
	
    }

    public function indexAction()
    {
    	
    	$this->view->titleView = $this->titleView;   	
  		$idStrategy = $this->getRequest()->getParam('idStrategy');
  		
    	$auth = Zend_Auth::getInstance();
  		if($auth->hasIdentity()) 
 		{
	    	// Query Vision of the user
			$idUser = $auth->getIdentity()->idUser;
        }
    	else
    	{
    		$this->view->NoLoginMessage = 'Login, and define your tasks';
    		return;
    	}
    	
  		if (isset($idStrategy) )
  		{
			$this->view->idStrategy = $idStrategy;
			$this->view->todotasks = $this->model->getTasksPerStrategyKeyPerCompleteStatus($idStrategy,'0');
			$this->view->inprogresstasks = $this->model->getTasksPerStrategyKeyPerCompleteStatus($idStrategy,'1');
			$this->view->completedtasks = $this->model->getTasksPerStrategyKeyPerCompleteStatus($idStrategy,'2');
			$titles = $this->modelStrategy->getTaskGoalStrategyTitles($idStrategy)->toArray();
			$this->view->strategyTitle = $titles['titleStrategy'];
			$this->view->goalTitle = $titles['titleGoal'];
  		}
  		else
  		{
			
  			if ( isset($idUser) )
			{
				$result = $this->getTasksPerUserKey($idUser);
				if ($result==0)
				{
					$this->view->NoLoginMessage = 'There are no tasks defined';
				}
			}
			else
			{
				$this->view->NoLoginMessage = 'Login, and define your tasks';
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
		require_once(APPLICATION_PATH . '/forms/Task.php');
		$form = new Form_Task();
		
		/* load goal id, only set when editing */
		$idTask = $this->getRequest()->getParam('idTask');
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
						$data['idStrategy'] = $idStrategy;
						$res = $this->model->insert($data);
					}
					else if($type == 'edit')
					{
						$res = $this->model->update($data, $idTask);
					}
					
					if(($type == 'add' && $res > 1) || ($type =='edit' && $res >= 1)) {
						return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'task', 'action' => 'index','idStrategy' => $idStrategy ),
							'default', true); 
					}else{
						echo 'Respuesta' . $res;
						$this->view->error = $res->getMessage();	
					}
				}
				else
				{
					$this->view->error = "Invalid Task";
				}
			}
			catch(Exception $e){
				$this->view->error = $e->getMessage();
			}
		}
		/* pass the form to the view */
		if($type == 'edit') $form->populate($this->model->get($idTask)->toArray());
		$this->view->form = $form;
	}
    
	public function deleteAction(){
		/* get user id*/
		$idStrategy = $this->getRequest()->getParam('idStrategy');
		$idTask = $this->getRequest()->getParam('idTask');
		/* delete user */
		$this->model->delete($idTask);
		
		return $this->_helper->redirector->gotoRoute(array(
			'controller' => 'task', 'action' => 'index','idStrategy' => $idStrategy),
			'default', true);
		
		
	}
    
	
	function getTasksPerUserKey($idUser)
	{

		$goals = $this->modelGoal->getGoalsPerUserKey($idUser);
	
		
		if ( $goals->count()==0)
		{
			return 0;
		}
		
		// Managing goals
		$thegoals = '';
		$init = 0;
		foreach ($goals as $goal )
		{
			$goalId = $goal['idGoal'];
			if ($init==0)
					$init=1;
			else
				$thegoals .= ' OR';
			$thegoals .= ' idGoal = ' . $goalId;						
		}

		$strategies = $this->modelStrategy->getStrategiesPerGoalKeys($thegoals);
		if ( $strategies->count()==0)
			return 0;
			
		//Managing strategies
		$init=0;
		$theStrategies = '';
		foreach( $strategies as $strategy )
		{
			$strategyId = $strategy['idStrategy'];
			if ($init==0)
				$init=1;
			else
				$theStrategies .= ' OR ';
			$theStrategies .= ' idStrategy = ' . $strategyId; 
		}

		$this->view->todotasks = $this->model->getTasksPerStrategyKeysPerCompleteStatus($theStrategies,'0');
		$this->view->completedtasks = $this->model->getTasksPerStrategyKeysPerCompleteStatus($theStrategies,'2');
		$this->view->inprogresstasks = $this->model->getTasksPerStrategyKeysPerCompleteStatus($theStrategies,'1');
		return 1;
	}
}
?>