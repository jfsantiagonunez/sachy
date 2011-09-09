<?php
class EvaluationController extends Zend_Controller_Action
{
	var $model,$modelGoal,$modelUser;
	var $titleView;
	
    public function init()
    {
        /* Load DB_model */

		require_once(APPLICATION_PATH . '/models/EvaluationModel.php');
		$this->model = new EvaluationModel();	
		require_once(APPLICATION_PATH . '/models/TblGoal.php');
		$this->modelGoal = new TblGoal();
		require_once(APPLICATION_PATH . '/models/TblUser.php');
		$this->modelUser = new TblUser();
		
		$this->titleView = "Strength and Weakness Evaluation";
    }

    public function indexAction()
    {
    	
    	$this->view->titleView = $this->titleView;
    		
 		$auth = Zend_Auth::getInstance();
  		if($auth->hasIdentity()) 
 		{
	    	// Query Vision of the user
			$idUser = $auth->getIdentity()->idUser;
        }
    	else
    	{
    		$this->view->NoLoginMessage = 'Login, and evaluate your goals';
    		return;
    	}
  		
    	$idGoal = $this->getRequest()->getParam('idGoal');
    	$this->view->evaluationblock= array();
    	
  		if (isset($idGoal) )
  		{
			//$this->view->idGoal = $idGoal;
			//$this->view->goalTitle = $this->modelGoal->getTitle($idGoal);
			//$this->view->evaluations = $this->model->getIndexActionEvaluationsPerGoalKey($idGoal );
			$this->view->evaluationblock[$idGoal]['idGoal'] = $idGoal;
			$this->view->evaluationblock[$idGoal]['goalTitle'] = $this->modelGoal->getTitle($idGoal);
			$this->view->evaluationblock[$idGoal]['evaluations'] = $this->model->getIndexActionEvaluationsPerGoalKey($idGoal );
			if ($this->view->evaluationblock[$idGoal]['evaluations']->count() == 0 )
			{
				// If there is none, then:
				return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'Evaluation', 'action' => 'evaluate','idGoal' => $idGoal ),
							'default', true); 
			}
			else
			{
				//$laststep = $this->view->evaluations[0]['laststep'];
				//if ($laststep!='6')
				//{
				//	return $this->_helper->redirector->gotoRoute(array(
				//			'controller' => 'Evaluation', 'action' => 'evaluate','idGoal' => $idGoal , 'step' => $laststep  ),
				//			'default', true);
				//}
			}
			// If there is then display
  		} 
  		else
  		{		
  			$idUserFromCoacher = $this->getRequest()->getParam('idUser');
  			
	  		if ( isset($idUserFromCoacher) )
  			{
  				// This is coming from Evaluation from Coacher
  				$this->getEvaluationsPerUserKey($idUserFromCoacher);
  			}
  			else
  			{
  				if ( isset($idUser) )
  				{
  					// This is coming from Evaluation from Coacher
  					$this->getEvaluationsPerUserKey($idUser);
  				}
  				else
  				{
  					$this->view->NoLoginMessage = 'Login, and define your strategies';
  					// print nice photo...
  				}
  			}			
  		}
    }
    
    
    public function coacherindexAction()
    {
    	
    	$this->view->titleView = $this->titleView;
    		
 		$auth = Zend_Auth::getInstance();
  		if($auth->hasIdentity()) 
 		{
	    	// Query Vision of the user
			$idCoacher = $auth->getIdentity()->idUser;

			$this->view->evaluations = $this->modelUser->getUsersPerCoacher($idCoacher );

        }
    	else
    	{
    		$this->view->NoLoginMessage = 'Login, and evaluate others as coacher';
    		return;
    	}
		
    }
    
    
   public function indexevaluationstepsAction()
   {
    	$this->view->titleView = $this->titleView;
    		
 		$auth = Zend_Auth::getInstance();
  		if($auth->hasIdentity()) 
 		{
	    	// Query Vision of the user
			$idUser = $auth->getIdentity()->idUser;
        }
    	else
    	{
    		$this->view->NoLoginMessage = 'Login, and evaluate your goals';
    		return;
    	}
  		
    	$idEvaluation = $this->getRequest()->getParam('idEvaluation');
    	
  		if (isset($idEvaluation) )
  		{
			$this->view->idEvaluation = $idEvaluation;
			$evaluation = $this->model->getEvaluation($idEvaluation);
			$this->view->goalTitle = $this->modelGoal->getTitle($evaluation['idGoal']);
			$this->view->evaluationSteps = $this->model->getIndexActionStepEvaluationsPerIdEvaluation($idEvaluation );
			if ($this->view->evaluationSteps->count() == 0 )
			{
				// If there is none, then:
				$this->view->NoLoginMessage = 'No Evaluation done. Please select a Goal and Evaluate';
			}
			else
			{
				//$laststep = $this->view->evaluations[0]['laststep'];
				//if ($laststep!='6')
				//{
				//	return $this->_helper->redirector->gotoRoute(array(
				//			'controller' => 'Evaluation', 'action' => 'evaluate','idGoal' => $idGoal , 'step' => $laststep  ),
				//			'default', true);
				//}
			}
			// If there is then display
  		} 
  		else
  		{		
	  		$this->view->NoLoginMessage = 'No Evaluations. Please select a Goal and Evaluate';
  		}
    }
    
    public function evaluateAction()
    {
   	
    	$this->view->titleView = $this->titleView;
		$step = $this->getRequest()->getParam('step');
		if (!isset($step))
		{
			$step = 1;
			
			$auth = Zend_Auth::getInstance();
  			if($auth->hasIdentity()) 
 			{
	    		// Query Vision of the user
				$idUser = $auth->getIdentity()->idUser;
        	}
    		else
    		{
    			$this->view->NoLoginMessage = 'Login, and evaluate your goals';
    			return;
    		}
			// Create Evaluation record
			$idGoal = $this->getRequest()->getParam('idGoal');
			if ( isset($idGoal))
			{
				$idEvaluation = $this->model->createEvaluationId($idGoal,$idUser);
				$this->view->goalTitle = $this->modelGoal->getTitle($idGoal);
			} 
			else
			{
				$this->view->NoLoginMessage = 'Select a Goal to start evalution';
				return;
			}
		}
		else
		{
			$idEvaluation = $this->getRequest()->getParam('idEvaluation');
			$evaluation = $this->model->getEvaluation($idEvaluation);
			$this->view->goalTitle = $this->modelGoal->getTitle($evaluation['idGoal']);
		}
		
		$stepString = (string) $step;
		$stepDict = $this->model->getStepDict($stepString)->toArray();
		
		$this->view->subtitleView = $stepDict['nameStep'];
		$this->view->subtitleView2 = $stepDict['descStep'];
		
    	$this->evaluateForm($step,$idEvaluation);
    }
    
    public function evaluateForm($step,$idEvaluation)
    {
    	require_once(APPLICATION_PATH . '/forms/Evaluation.php');
		$form = new Form_Evaluation();
		
		/* if this is a post and it's valid */
		if($this->getRequest()->isPost()){
			try{
				/* determine type and save in the db */
				if($form->isValid($this->getRequest()->getPost() ) )
				{
					$data = $form->getValues();	
					$data['idStep'] = $step;
					$data['idEvaluation'] = $idEvaluation;
					$res = $this->model->insertStep($data);

					if($res > 0)  {
						$step = $this->model->nextStep($step);
						if ($step == 0 )
						{
							$evaluation = $this->model->getEvaluation($idEvaluation)->toArray();
							$idGoal = (string) $evaluation['idGoal'];
							return $this->_helper->redirector->gotoRoute(array(
								'controller' => 'Evaluation', 'action' => 'index','idGoal' => $idGoal  ),
								'default', true); 
						}
						else
						{
							return $this->_helper->redirector->gotoRoute(array(
								'controller' => 'Evaluation', 'action' => 'evaluate','idEvaluation' => $idEvaluation , 'step' => $step ),
								'default', true); 
						}
					}else{
						$this->view->error = $res->getMessage();	
					}
				}
				else
				{
					$this->view->error = "Invalid Evaluation";
				}
			}
			catch(Exception $e){
				$this->view->error = $e->getMessage();
			}
		}

		$this->view->form = $form;
    }

    
    public function editstepAction()
    {
    	$keyStep = $this->getRequest()->getParam('keyStep');
		if (!isset($keyStep))
		{
			return;
		}
    	
    	require_once(APPLICATION_PATH . '/forms/Evaluation.php');
		$form = new Form_Evaluation();
		
		$step = $this->model->getEvaluationStep($keyStep)->toArray();
		$idEvaluation = (string) $step['idEvaluation'];
							
		/* if this is a post and it's valid */
		if($this->getRequest()->isPost()){
			try{
				/* determine type and save in the db */
				if($form->isValid($this->getRequest()->getPost() ) )
				{
					$data = $form->getValues();	
					$data['keyStep'] = $keyStep;
					$res = $this->model->updateStep($data);

					if($res >= 0)  {
						
							
							return $this->_helper->redirector->gotoRoute(array(
								'controller' => 'Evaluation', 'action' => 'indexevaluationsteps','idEvaluation' => $idEvaluation  ),
								'default', true); 
						
					}
					
					else{
						$this->view->error = $res->getMessage();	
					}
				}
				else
				{
					$this->view->error = "Invalid Evaluation";
				}
			}
			catch(Exception $e){
				$this->view->error = $e->getMessage();
			}
		}
		$step = $this->model->getEvaluationStep($keyStep)->toArray();
		$form->populate($step);
		$stepString = (string) $step['idStep'];
		$stepDict = $this->model->getStepDict($stepString)->toArray();
		$this->view->subtitleView = $stepDict['nameStep'];
		$this->view->subtitleView2 = $stepDict['descStep'];
		// Get Goal Id from keyStep
		$evaluation = $this->model->getEvaluation($idEvaluation);
		$this->view->goalTitle = $this->modelGoal->getTitle($evaluation['idGoal']);
		
		$this->view->form = $form;

    }
    
    

    
	public function deleteAction(){

		$idEvaluation = $this->getRequest()->getParam('idEvaluation');
		
		if (isset($idEvaluation))
		{
			$evaluation = $this->model->getEvaluation($idEvaluation);
			$idGoal = (string) $evaluation['idGoal'];
			/* delete user */
			$this->model->deleteEvaluation($idEvaluation);
		
			return $this->_helper->redirector->gotoRoute(array(
				'controller' => 'Evaluation', 'action' => 'index','idGoal' => $idGoal),
				'default', true);
		}
		
		$keyStep = $this->getRequest()->getParam('keyStep');
		if (isset($keyStep))
		{
			$step = $this->model->getEvaluationStep($keyStep)->toArray();
			$idEvaluation = (string) $step['idEvaluation'];
			
			$this->model->deleteStepEvaluation($keyStep);
						
			return $this->_helper->redirector->gotoRoute(array(
						'controller' => 'Evaluation', 'action' => 'indexevaluationsteps','idEvaluation' => $idEvaluation  ),
						'default', true); 

		}
		
	}
    
	
	function getEvaluationsPerUserKey($idUser)
	{
		require_once(APPLICATION_PATH . '/models/TblGoal.php');
		$modelGoal = new TblGoal();

		$goals = $modelGoal->getGoalsPerUserKey($idUser);
	
		
		if ( $goals != null)
		{
			foreach ($goals as $goal )
			{
				$idGoal = $goal['idGoal'];
			
				$this->view->evaluationblock[$idGoal]['idGoal'] = $idGoal;
				$this->view->evaluationblock[$idGoal]['goalTitle'] = $this->modelGoal->getTitle($idGoal);
				$this->view->evaluationblock[$idGoal]['evaluations'] = $this->model->getIndexActionEvaluationsPerGoalKey($idGoal );
			}

		}

	}
	
	public function createstrategiesAction()
	{
		$idGoal = $this->getRequest()->getParam('idGoal');
		
		if (isset($idGoal))
		{
			require_once(APPLICATION_PATH . '/models/TblStrategy.php');
			$modelStrategy = new TblStrategy();
			$weakness = array();
			$weaknesses = $this->model->getWeaknessPerGoal($idGoal)->toArray();
			
			foreach ($weaknesses as $weakness)
			{

				if (!isset($weakness['idStrategy']))
				{
					
					$newstrategy = array();
					$stepString = (string) $weakness['idStep'];
					$stepDict = $this->model->getStepDict($stepString);
					$newstrategy['titleStrategy'] = $stepDict['nameStep'] . ' Strategy' ;
					$newstrategy['descStrategy'] = 'Work on [' . $weakness['weakness'] . ']';
					$newstrategy['idGoal'] = $idGoal;
					$idStrategy = $modelStrategy->insert($newstrategy);
					
					$this->model->updateStepEvaluationWithStrategy($weakness['keyStep'], $idStrategy );
				}
				
			}
			
			return $this->_helper->redirector->gotoRoute(array(
						'controller' => 'strategy', 'action' => 'index','idGoal' => $idGoal  ),
						'default', true); 
		}
		else
		{
			
		}
	}
}
?>