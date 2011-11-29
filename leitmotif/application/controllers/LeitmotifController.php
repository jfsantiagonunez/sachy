<?php
require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class LeitmotifController extends BaseController
{
	var $model;
	var $errorlogin;
	var $titleView;
	
	protected $_idkey = 'notodefine';
	protected $_controllername = 'user';
	
    public function init()
    {
        /* Load DB_model */

		require_once(APPLICATION_PATH . '/models/ModelLeitmotif.php');
		$this->model = new ModelLeitmotif();		
		$this->titleView = "Leitmotif Levels";
    }

    public function indexAction()
    {
   		$this->indexbaseAction();
    }
    
    public function indexsubaction($idUser)
    {     	
 		$params = $this->getRequest()->getParams();

    	$this->view->output = $this->returnleitmotif($params,$idUser);
    	
   	}
    
   	public function returnleitmotif(array $params,$idUser)
   	{
   		// Posibilities
   		// + (A) No parameters. Show level 0 for this idUser
   		//       (Criteria , idUser & level =0 )
   		//
   		// + (B) idLeitmotif , $step = -1. Show upper for this idLeitmotif.
   		//   We could just show the parent. But better show all the leitmotif with at higher level with the same parent!!!
   		//       (Criteria idUser and level (idLeitmotif.level-1) )
   		//
   		// + (C) idLeitmotif, $step = 1. Show children leitmotifs for this idLeitmotif
   		//       (Criteria idParent=idLeitmotif )
   		//
   		// + (D) level. Show all leitmotif at that level
   		//       (Criteria idUser & level )
   		//
   		// + (E) idLeitmotif. Just that leitmotif
   		//       (Criteria idLeitmotif)
   		
   		$step = $params['step'];
   		$idLeitmotif = $params['idLeitmotif'];
   		$level = $params['level'];
   		$parent = $params['idUp']; // The id of the List. Will be used to embedded the id of the parent of that list,
   					   // in order to use it for create elements.
   		//print_r($params);
   		$leitmotifs = array();
   		 
   		if (isset($level))
   		{   // Case E. All leitmotifs at that level
   			$leitmotifs = $this->model->queryLeitmotifPerUser($level,$idUser)->toArray();
   			$parent = "Notallowed";
   		}
   		else if (isset($idLeitmotif))
   		{
   			$thisLeitmotif = $this->model->queryID('TblLeitmotif', $idLeitmotif);
   			$level = $thisLeitmotif['level'];
   			$levelnum = (int) $level;
   			
   			if ( isset($step) )
   			{
	   			
	   			// Just query above or below that level or bel
	   			$stepnum =(int)$step;
	   			// Level of the results
	   			$level = $levelnum + $stepnum;
	   			
	   			if ( $stepnum > 0 )
	   			{ // Case C. Return Children of a leitmotif
	   				
	   				$leitmotifs = $this->model->queryChildrenLeitmotifs($idLeitmotif)->toArray();
	   				$parent = $idLeitmotif;
	   				
	   			}
	   			else
	   			{ // Case B. Return parent (and then other leitmotifs at that level of the same parent!!!) of a leitmotif
	   				// Get the grant-parent
	   				if ($parent != '0')
	   				{
	   					$grantparent = $this->model->queryId('TblLeitmotif',$parent);
	   					
	   					$idgrantparent = $grantparent['idUp'];
	   					if ($idgrantparent != '0')
	   					{
	   						$leitmotifs = $this->model->queryChildrenLeitmotifs($idgrantparent)->toArray();
	   						$parent = $idgrantparent;
	   					}
	   					else
	   					{
	   						$leitmotifs = $this->model->queryLeitmotifPerUser($level,$idUser)->toArray();
	   						
	   					}
	   				}
	   				else
	   				{
	   					$leitmotifs = $this->model->queryLeitmotifPerUser($level,$idUser)->toArray();
	   				}
	   				
	   				
	   			}
	   			   			
   			}
   			else
   			{  // Case E. Specific query of that leitmotif
   				$leitmotifs[]=$thisLeitmotif;
   			}
   		}
   		else
   		{
   			// Case A. No parameters. Mostrar Mision
   			$level="0";
   			$parent="0";
   			$leitmotifs = $this->model->queryLeitmotifPerUser($level,$idUser)->toArray();
   		}
   		
   		// Before call this. make sure we set the $level !!!! 		
   		return $this->model->buildOutput($leitmotifs,$level,$parent) ;
   	}
   	
   	public function retrieveAction()
   	{
   		$auth = Zend_Auth::getInstance();
   		if($auth->hasIdentity())
   		{
   			//$this->_helper->viewRenderer->setNoRender();
   			$this->_helper->layout->disableLayout();
   			$params = $this->getRequest()->getParams();
   			
   			$idUser = $auth->getIdentity()->idUser;
   			print( $this->returnleitmotif($params,$idUser) );
  
   		}
   		
   	}
   	
   	public function addeditleitmotifAction()
   	{
   		//$this->_helper->viewRenderer->setNoRender();
   		$this->_helper->layout->disableLayout();
   		$data = array();
   		
   		if($this->getRequest()->isGet())
   		{ 	
   			//print ('GET');
   			$data = $this->getRequest()->getParams();
   			//print_r($data);
   		}
   		else if($this->getRequest()->isPost())
   		{ 
   			//print ('POST');
   			$data = $this->getRequest()->getPost();
   			//print_r($data);
   		}
   		else if ($this->getRequest()->isXmlHttpRequest())
   		{
   			//print ('AJAX');
   		}
   		else
   		{
   			//print ('MMMM');
   		}

   		if (isset($data) )
   		{ 		
   			return $this->model->createUpdateLeitmotif($data);
   		}
   		  		
   	}
   	
   	public function deleteajaxAction()
   	{
   		$auth = Zend_Auth::getInstance();
   		if($auth->hasIdentity())
	   	{
	   		$this->_helper->viewRenderer->setNoRender();
	   		$this->_helper->layout->disableLayout();
	   	
	   		$data = $this->getRequest()->getParams();
	   	
	   		$idValue = $data['idLeitmotif'];
	   		if (isset($idValue))
	   		{
	   			$this->model->delete('idLeitmotif',$idValue);
	   		}
   		}
   	
   	}
   
}
?>