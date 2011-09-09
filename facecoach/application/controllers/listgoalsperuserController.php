<?php

class ListGoalsPerUserController extends Zend_Controller_Action
{
	var $model;
	var $xmlOutput;
	var $numerResults;
	
    public function init()
    {
        /* Load DB_model */
		require_once(APPLICATION_PATH . '/models/TblGoal.php');
		require_once(APPLICATION_PATH . '/models/TblLogDairy.php');
		require_once(APPLICATION_PATH . '/models/TblLogDairyGoalRelationship.php');
		require_once(APPLICATION_PATH . '/models/TblStrategy.php');
		require_once(APPLICATION_PATH . '/models/TblTask.php');
		require_once(APPLICATION_PATH . '/models/TblUser.php');
		$this->modelTblGoal = new TblGoal();
		$this->modelTblLogDairy = new TblLogDairy();
		$this->modelTblLogDairyGoalRelationship = new TblLogDairyGoalRelationship();
		$this->modelTblStrategy = new TblStrategy();
		$this->modelTblTask = new TblTask();
		$this->modelTblUser = new TblUser();
						
    }

    // Example query : :@"http://Joses-MacBook.local/mygoals/public/listgoalsperuser/index/user/%@"
    
    public function indexAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$userName = $this->getRequest()->getParam('user');
    	$this->xmlOutput = '';
    	
   		if ($userName!=null) 	
   		{
    	 	$this->getContentPerUser($userName);
   		}
     	
    	$this->view->xml = '<mygoalsdata>';
		$this->view->xml .= $this->xmlOutput;    	
    	$this->view->xml .= '</mygoalsdata>';
    	
    }
    
    protected function getContentPerUser($key)
    {
   		$result = $this->modelTblUser->getUserKey($key);

   		if ( $result->count() > 0 )
   		{
   			$userKey = $result[0]['idUser'];
   	 		if ($userKey != null )
   	 		{
   	 			$this->xmlOutput .= '<user name=' . $key . ' id=' . $userKey . '>';
    	 		$this->getGoalsPerUser($userKey);
    	 		$this->getLogsPerUser($userKey);
    	 		$this->xmlOutput .= '</user>';
   	 		}
   		}
    }

    
    protected function getGoalsPerUser( $key )
    {
    	$results = array();
     	$results = $this->modelTblGoal->getGoalsPerUserKey($key);
     	
       	if ( $results != null){
       		$this->xmlOutput .= '<goals>';	    		
	    		foreach($results as $theResult){
	    			$thisKey = $theResult['idGoal'];
	    			$this->xmlOutput .= '<goal id=' . $thisKey . '>';
	    			$this->xmlOutput .='<title>' . urlencode($theResult['titleGoal']) . '</title>';
	    			$this->xmlOutput .= '<desc>' . urlencode($theResult['descGoal']) . '</desc>';
	    			$this->getStrategiesPerGoal($thisKey);
	    			$this->xmlOutput.= '</goal>';   
	    		}
	    	$this->xmlOutput .= '</goals>';
    	}

    }
    
    
   protected function getStrategiesPerGoal( $key )
    {
    	$results = array();
     	$results = $this->modelTblStrategy->getStrategiesPerGoalKey($key);
     	
       	if ( $results != null){
       		$this->xmlOutput .= '<strategies>';
	    		foreach($results as $theResult){
   	    			$thisKey = $theResult['idStrategy'];
	    			$this->xmlOutput .= '<strategy id='. $thisKey . '>';
	    			$this->xmlOutput .='<title>' . urlencode($theResult['titleStrategy']) . '</title>';
	    			$this->xmlOutput .= '<desc>' . urlencode($theResult['descStrategy']) . '</desc>';
	    			$this->getTasksPerStrategy($thisKey);
	    			$this->xmlOutput.= '</strategy>';   
	    		}
	    	
	    	$this->xmlOutput .= '</strategies>';
    	}

    }
    
  	protected function getTasksPerStrategy( $key )
    {
    	$results = array();
     	$results = $this->modelTblTask->getTasksPerStrategyKey($key);
     	
       	if ( $results != null){
       		$this->xmlOutput .= '<tasks>';
	    		foreach($results as $theResult){
   	    			$thisKey = $theResult['idTask'];
	    			$this->xmlOutput .= '<task id='. $thisKey . '>';
	    			$this->xmlOutput .='<title>' . urlencode($theResult['titleTask']) . '</title>';
	    			$this->xmlOutput .= '<desc>' . urlencode($theResult['descTask']) . '</desc>';
	    			$this->getTasksPerStrategy($thisKey);
	    			$this->xmlOutput.= '</task>';   
	    		}
	    	$this->xmlOutput .= '</tasks>';
    	}

    }
    
    
    protected function getLogsPerUser( $key )
    {
     	$results = $this->modelTblLogDairy->getLogsPerUserKey($key);
     	
       	if ( $results != null){
       		$this->xmlOutput .= '<logDiaries>';
	    		foreach($results as $theResult){
	    			$thisKey = $theResult['idTblLogDiary'];
	    			$this->xmlOutput .= '<logDiary id='. $thisKey . '>';
	    			$this->xmlOutput .='<text>' . urlencode($theResult['logText']) . '</text>';
	    			$this->xmlOutput .= '<date>' . urlencode($theResult['logDate']) . '</date>';
	    			
	    			$this->xmlOutput.= '</logDiary>';   
	    		}
	    	$this->xmlOutput .= '</logDiaries>';
    	}

    }
}

?>