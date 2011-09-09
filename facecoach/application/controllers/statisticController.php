<?php

class StatisticController extends Zend_Controller_Action
{
	var $Emodel,$Tmodel,$Gmodel,$Smodel,$Cmodel;
	var $titleView;
	var $counter;
	
    public function init()
    {
 		require_once(APPLICATION_PATH . '/models/TblStrategy.php');
		$this->Smodel = new TblStrategy();
 		require_once(APPLICATION_PATH . '/models/TblEvent.php');
		$this->Emodel = new TblEvent();
		require_once(APPLICATION_PATH . '/models/TblTask.php');
		$this->Tmodel = new TblTask();
		require_once(APPLICATION_PATH . '/models/TblGoal.php');
		$this->Gmodel = new TblGoal();
		require_once(APPLICATION_PATH . '/models/TblCalendar.php');
		$this->Cmodel = new TblCalendar();
		$this->titleView = "Statistics :  Monitor your progress";
    }
    
 	public function indexAction () {
 		$this->view->titleView = $this->titleView;
	    $auth = Zend_Auth::getInstance();
	    
		if($auth->hasIdentity()) 
 		{
	    	// Query Vision of the user
			$idUser = $auth->getIdentity()->idUser;
        }
    	else
    	{
    		$this->view->NoLoginMessage = 'Login, and monitor your Progress';
    		return;
    	}
    	
		if ( !isset($idUser) )
		{
			$this->view->NoLoginMessage = 'Login, and monitor your progress';
			// print nice photo...
			return;
		}
		
		// Get stats for Goals
		$Gstats = $this->Gmodel->getStats('idUser = ' . $idUser);
		$this->view->statisticsTable .= 'Goals : ' . $Gstats['count'] . '<br>';
		$stats = $this->getStats($idUser);
		$this->view->statisticsTable .= 'Strategies : ' . $stats['strategies'] . '<br>';
		$this->view->statisticsTable .= 'Tasks : ' . $stats['tasks'] . '<br>';
		$this->view->statisticsTable .= 'Events : ' . $stats['events'] . '<br>';
				
 	}
 	
	function getStats($idUser)
	{
		$goals = $this->Gmodel->getGoalsPerUserKey($idUser);
	
		
		
		if ( $goals != null)
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
		}
		
		$stats['strategies'] = $this->Smodel->getCount($thegoals);
		
		// Getting Tasks
		$strategies = $this->Smodel->getStrategiesPerGoalKeys($thegoals);
		if ( $strategies == null )
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

		$stats['tasks'] = $this->Tmodel->getCount($theStrategies);
		$tasks =  $this->Tmodel->getTasksPerStrategyKeys($theStrategies);
		
		$calendars = $this->Cmodel->getCalendarsPerUserKey($idUser);
		
		if ( $calendars == null )
		{
			return null;
		}
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

		$stats['events'] = $this->Emodel->getCount($thecals);
		
		// Get Events
		return $stats;
	}
}
?>
