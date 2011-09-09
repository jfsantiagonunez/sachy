<?php

class EventController extends Zend_Controller_Action
{
	var $model,$Cmodel,$taskModel;

    public function init()
    {
 		require_once(APPLICATION_PATH . '/models/TblCalendar.php');
		$this->Cmodel = new TblCalendar();
 		require_once(APPLICATION_PATH . '/models/TblEvent.php');
		$this->model = new TblEvent();
		require_once(APPLICATION_PATH . '/models/TblTask.php');
		$this->taskModel = new TblTask();
		$this->titleView = "Calendar Event :  BOOK time to realize your tasks";
    }
    
 	public function indexAction () {
 				
	    $auth = Zend_Auth::getInstance();
	    
		if($auth->hasIdentity()) 
 		{
	    	// Query Vision of the user
			$idUser = $auth->getIdentity()->idUser;
        }
    	else
    	{
    		$this->view->NoLoginMessage = 'Login, and define your Calendar Events';
    		return;
    	}
    	
    	$idTask =  $this->getRequest()->getParam('idTask');
 	  	if (isset($idTask) )
  		{
			$this->view->idTask = $idTask;
			$this->view->events = $this->model->getEventsPerTaskId($idTask);
  		}
  		else
  		{
			
  			if ( isset($idUser) )
			{
				$this->view->events = $this->getEventsPerUserKey($idUser);
			}
			else
			{
				$this->view->NoLoginMessage = 'Login, and define your tasks';
				// print nice photo...
			}

  		}
	}
	
	public function addAction()
	{
		$this->saveEventCalendar('add');
	}
	
	public function editAction()
	{
		$this->saveEventCalendar('edit');
	}
	
	function saveEventCalendar($type)
	{
		require_once(APPLICATION_PATH . '/forms/event.php');
		$form = new Form_Event();
		
		$idUser = $this->getRequest()->getParam('idUser');
		if (!isset($idUser))
		{
			$auth = Zend_Auth::getInstance();
	    
			if($auth->hasIdentity()) 
 			{
	    		// Query Vision of the user
				$idUser = $auth->getIdentity()->idUser;
        	}
        	else
        	{
    			$this->view->NoLoginMessage = 'Login, and define your Calendar Events';
    			return;	
        	}
		}
		$idTask = $this->getRequest()->getParam('idTask');
		$idEvent = $this->getRequest()->getParam('idEvent');
		
		if (!($this->getRequest()->isPost()))
		{ 
			// Get posible calendars
			$calendars = $this->Cmodel->getAllCalendarsToArray($idUser);
			/* pass the form to the view */
			if($type == 'edit')
			{
				$data2=$this->model->get($idEvent)->toArray();
				$form->addTaskCalendar($data2['title'],$calendars);
			}
			else
			{
				$task = $this->taskModel->get($idTask)->toArray();
				$titletask = $task['titleTask'];
				$form->addTaskCalendar('MyTask:' . $titletask,$calendars);
			}
		}
		if ($this->getRequest()->isPost()) {
		try{
			/* determine type and save in the db */
			if($form->isValid($this->getRequest()->getPost() ) )
			{
				$data = $form->getValues();	
	
				$data['idTask'] = $idTask;
				if($type == 'add') 
				{
					$gmailEvent = $this->createEventOnCalendar($data,$idTask);
					
					
					$data['idHrefGcal'] = $gmailEvent->getEditLink()->href;
					$data['link'] = $gmailEvent->getAlternateLink()->href;
					$res = $this->model->createEvent($data);
					if ( $res != '0' )
					{
						return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'event', 'action' => 'index' , 'idTask' => $idTask),
						'default', true);
					
					}
					else
					{ 
						$this->view->error = "Calendar Event already exists";
					}
				}
				else if($type == 'edit')
				{
					$data['idEvent'] = $idEvent;
					$data['idTask'] = $idTask;
					$res = $this->model->update($data, $idEvent);
				
					if ( $res == 1) {
							return $this->_helper->redirector->gotoRoute(array(
								'controller' => 'event', 'action' => 'index', 'idTask' => $idTask),
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

		if($type == 'edit')
		{
			$form->populate($this->model->get($idEvent)->toArray());
		}
		$this->view->form = $form;
	}
	
	public function deleteAction(){
		
		$idTask = $this->getRequest()->getParam('idTask');
		$idEvent = $this->getRequest()->getParam('idEvent');
		try{
			$this->deleteGcalEvent($idEvent);
		}
		catch (Exception $e){echo "Error: " . $e->getMessage();}
		$this->model->delete($idEvent);
		return $this->_helper->redirector->gotoRoute(array(
			'controller' => 'event', 'action' => 'index', 'idTask' => $idTask ),
			'default', true);
		
		
	}
	
	public function createEventOnCalendar($data,$idTask) {
		$task = $this->taskModel->get($idTask)->toArray();
		$desctask = $task['descTask'];				

		$calAccount = $this->Cmodel->get($data['idCalendar'])->toArray();
		// Parameters for ClientAuth authentication
		$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
		$user = $calAccount['email'];
		$pass = $calAccount['password'];
 		
		// Create an authenticated HTTP client
		$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
 
		// Create an instance of the Calendar service
		$service = new Zend_Gdata_Calendar($client);

		// Create a new entry using the calendar service's magic factory method
		$event= $service->newEventEntry();

		// Populate the event with the desired information
		// Note that each attribute is crated as an instance of a matching class
		$event->title = $service->newTitle($data['title']);
		$event->where = array($service->newWhere($data['where']));
		$event->content = $service->newContent( $desctask);
 
		// Set the date using RFC 3339 format.
		$startDate = $data['startdate'];
		$startTime = $data['starttime'];
		$endDate = $data['enddate'];
		$endTime = $data['endtime'];
		$tzOffset = "+02";
 
		$when = $service->newWhen();
		$when->startTime = "{$startDate}T{$startTime}:00.000{$tzOffset}:00";
		$when->endTime = "{$endDate}T{$endTime}:00.000{$tzOffset}:00";
		//$when->startTime = "{$startDate}T{$startTime}:00Z";
		//$when->endTime = "{$endDate}T{$endTime}:00Z";
		$event->when = array($when);
 
		// Add Reminder, Email method
		$reminder = $service->newReminder();
		$reminder->method = $calAccount['notification'];
		$reminder->minutes = "10";
		$when = $event->when[0];
		$when->reminders = array($reminder);

		// Upload the event to the calendar server
		// A copy of the event as it is recorded on the server is returned

		$newEvent = $service->insertEvent($event);
		return $newEvent;
	}
	
	public function deleteGcalEvent($idEvent)
	{
		$myevent = $this->model->get($idEvent)->toArray();
		$calAccount = $this->Cmodel->get($myevent['idCalendar'])->toArray();
		// Parameters for ClientAuth authentication
		$service = Zend_Gdata_Calendar::AUTH_SERVICE_NAME;
		$user = $calAccount['email'];
		$pass = $calAccount['password'];
 		
		// Create an authenticated HTTP client
		$client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
 
		// Create an instance of the Calendar service
		$service = new Zend_Gdata_Calendar($client);
		$service->delete($myevent['idHrefGcal']);
	}
	
	
	public function getEventsPerUserKey($idUser)
	{
		$calendars = $this->Cmodel->getCalendarsPerUserKey($idUser);
		
		if ( $calendars->count() > 0 )
		{
			$thecals = '';
			$init=0;
			foreach( $calendars as $calendar)
			{
				$calId = $calendar['idCalendar'];

				if ($init==0)
				{
					$init=1;
				}
				else
				{
					$thecals .= ' OR';
				}
				$thecals .= ' idCalendar = ' . $calId;
			}

			return $this->model->getEventsPerCalendarIds($thecals);
		}
		else
		{
			return null;
		}
		
	}
}

?>