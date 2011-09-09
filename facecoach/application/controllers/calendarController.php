<?php

class CalendarController extends Zend_Controller_Action
{
	var $model;
	
    public function init()
    {
 		require_once(APPLICATION_PATH . '/models/TblCalendar.php');
		$this->model = new TblCalendar();
		$this->titleView = "Calendar Event :  BOOK time to realize your tasks";
    }
    
 	public function indexAction () {
 		

 		
	    $auth = Zend_Auth::getInstance();
    	if($auth->hasIdentity()) 
    	{
    		// Query Vision of the user
			$idUser = $auth->getIdentity()->idUser;
			$this->view->idUser = $idUser;
			$this->view->calendars = $this->model->getCalendarsPerUserKey($idUser);
    	}
    	else
    	{
    	   		return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'user', 'action' => 'login'),
							'default', true );
    	}
		
	}
	
	
	public function addAction()
	{
		$this->savecalendar('add');
	}
	
	public function editAction()
	{
		$this->savecalendar('edit');
	}
	
	function savecalendar($type)
	{
		require_once(APPLICATION_PATH . '/forms/Calendar.php');
		$form = new Form_Calendar();
		
		
		$idUser = $this->getRequest()->getParam('idUser');
		$idCal = $this->getRequest()->getParam('idCalendar');
		
		if ($this->getRequest()->isPost()) {
		try{
			/* determine type and save in the db */
			if($form->isValid($this->getRequest()->getPost() ) )
			{
				$data = $form->getValues();	
				$data['idUser'] = $idUser;
				if($type == 'add') 
				{
					$res = $this->model->createCalendar($data);
					if ( $res != '0' )
					{

						return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'calendar', 'action' => 'index' , 'idUser' => $idUser),
							'default', true);
					}
					else
					{ 
						$this->view->error = "Calendar already exists";
					}
				}
				else if($type == 'edit')
				{
					$data['idCalendar'] = $idCal;
					$data['idUser'] = $idUser;
					$res = $this->model->update($data, $idCal);
				
					if ( $res == 1) {
							return $this->_helper->redirector->gotoRoute(array(
								'controller' => 'calendar', 'action' => 'index', 'idUser' => $idUser),
								'default', true); 
					}else{
						$this->view->error = 'Response' . $res;	
					}
				}
			}
			}
			catch(Exception $e){
				$this->view->error = $e->getMessage();
			}
		}

		/* pass the form to the view */
		if($type == 'edit')
		{
			$form->populate($this->model->get($idCal)->toArray());
		}
		$this->view->form = $form;
	}
	
	
	
	public function deleteAction(){
		/* get user id*/
		$idCalendar = $this->getRequest()->getParam('idCalendar');
		$idUser = $this->getRequest()->getParam('idUser');
		/* delete user */
		$this->model->delete($idCalendar);
		
		return $this->_helper->redirector->gotoRoute(array(
			'controller' => 'calendar', 'action' => 'index', 'idUser' => $idUser ),
			'default', true);
		
		
	}
}

?>