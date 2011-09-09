<?php
class LogDairyController extends Zend_Controller_Action
{
	var $model;
	var $titleView;
	
    public function init()
    {
        /* Load DB_model */

		require_once(APPLICATION_PATH . '/models/TblLogDairy.php');
		$this->model = new TblLogDairy();	
		$this->titleView = "LogDairy : WHAT is happening";
    }

    public function indexAction()
    {
    	$this->view->goals = 'Write WHAT is happening';
    	$this->view->titleView = $this->titleView;
    	
    	
    	$auth = Zend_Auth::getInstance();
    	if($auth->hasIdentity()) 
    	{
    		// Query Vision of the user
			$idUser = $auth->getIdentity()->idUser;
			$this->view->idUser = $idUser;
			$lastweek = time() - (7*24*60*60);
			
			$this->view->logs = $this->model->getLogsPerUserKey($idUser, date('d-m-Y', $lastweek));
    	}
    	else
    	{
    		$this->view->NoLoginMessage = 'Login, and write your logs';
    	}
    }

    public function addAction(){
	
		$this->view->titleView = $this->titleView;
		require_once(APPLICATION_PATH . '/forms/LogDairy.php');
		$form = new Form_LogDairy();
		
		$idUser = $this->getRequest()->getParam('idUser');
		/* if this is a post and it's valid */
		if($this->getRequest()->isPost()){
			try{
				/* determine type and save in the db */
				if($form->isValid($this->getRequest()->getPost() ) )
				{
					$data = $form->getValues();	
					 
					$data['idUser'] = $idUser;
					$data['logDate'] = date('Y-m-d h:m:s');
					echo $data['logDate'];
					$res = $this->model->insert($data);

					if( $res > 1)  {
						return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'logdairy', 'action' => 'index'),
							'default', true); 
					}else{
						$this->view->error = $res->getMessage();	
					}
				}
				else
				{
					$this->view->error = "Invalid Log";
				}
			}
			catch(Exception $e){
				$this->view->error = $e->getMessage();
			}
		}
		/* pass the form to the view */
		$this->view->form = $form;
	}
 
	public function deleteAction(){

		$idLog = $this->getRequest()->getParam('idTblLogDairy');
		
		$this->model->delete($idLog);
		
		return $this->_helper->redirector->gotoRoute(array(
			'controller' => 'logdairy', 'action' => 'index'),
			'default', true);
		
		
	}
    
}
?>