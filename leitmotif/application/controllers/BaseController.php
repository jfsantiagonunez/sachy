<?php
class BaseController extends Zend_Controller_Action
{
	protected $_idkey = 'id';
	protected $model;
	protected $_controllername = 'Base';

	public function init()
    {
		
    }

	public function disableLayout()
	{
		$this->_helper->viewRenderer->setNoRender();
		$this->_helper->layout->disableLayout();
	
		
	}
	
    public function indexAction()
    {
   		$this->indexbaseAction();
    }
    
    
	public function indexajaxAction()
	{
		$this->disableLayout();
		$auth   = Zend_Auth::getInstance();
		if(!$auth->hasIdentity()){
			echo "LOGIN REQUIRED.";
			return;
		}
		$idUser = $auth->getIdentity()->idUser;

		if ( empty($idUser) )
		{
			echo "INTERNAL ERROR. Unknown user";
			return;
		}

		$idParent = $this->getRequest()->getParam('idParent');

		$this->queryTopics($idParent,$idUser);
		 
	}

	public function localQueryTopic($idParent)
	{
		$this->view->thisItem =  $this->model->queryID($this->_mainTable,$idParent)->toArray();
	}
	
	public function queryTopics($idParent,$idUser)
	{

		if ( empty($idParent) || ($idParent === 'top')) {
			$idParent = 'top';	
		}
		else
		{
			$this->localQueryTopic($idParent);
		}

		//print_r($this->view->data);
		$this->view->headerlevel = 'h2';
		$this->view->canaddnew=true;
		$this->view->idParent=$idParent;
		$this->view->accordion = 'accordion-'.$this->_controllername.'-'.$idParent;
		$this->view->controller = $this->_controllername;
		$this->view->namePk = $this->model->getTable($this->_mainTable)->getPK();
		$this->view->textField = $this->model->getTable($this->_mainTable)->getTextField();
		
		$rows = $this->localQueryTopics($idParent,$idUser);
		
		if ($rows->count()>0)
		{
			$this->view->data = $rows->toArray();
		}
		
		echo $this->view->render('base/output.ajax.phtml');
	}
   
	
	public function deleteajaxAction()
	{
		$this->disableLayout();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			 
			$idParent = $this->getRequest()->getParam('idParent');
            $idUser = $auth->getIdentity()->idUser;
			
            $namePk = $this->getRequest()->getParam('namePk');
			$idValue = $this->getRequest()->getParam('idValue');
			if (isset($idValue))
			{
				$this->model->delete($namePk,$idValue);
			}
			$this->queryTopics($idParent,$idUser);
		}

	}
	
	public function newajaxAction()
	{
		$this->disableLayout();

		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			$idParent = $this->getRequest()->getParam('idParent');
            $idUser = $auth->getIdentity()->idUser;
            $textleitmotif = $this->getRequest()->getPost('texttopic', null);
            
			if (!empty($textleitmotif))
			{
				$data =  array();
				$data['idUser'] = $idUser;
				if (!empty($idParent) && isset($idParent)) {
					$data['idParent']=$idParent;
				}
				$data['texttopic']=$textleitmotif;
				$this->model->createTopic($this->_mainTable,$data);
			}
			else
			{
				echo 'INTERNAL ERROR. EMPTY TEXT'; return;
			}
			
			$this->queryTopics($idParent,$idUser);
			
		}
	}
	
		public function updateajaxAction()
	{
		$this->disableLayout();
		$auth = Zend_Auth::getInstance();
		if($auth->hasIdentity())
		{
			 
			$idParent = $this->getRequest()->getParam('idParent');
            $idUser = $auth->getIdentity()->idUser;
			 
            $TblTable = $this->model->getTable($this->_mainTable);
            $idValue = $this->getRequest()->getParam($TblTable->getPK());
            $param = $this->getRequest()->getParam('param');

            if ( isset($idValue) && isset($param) )
            {
            	$value= $this->getRequest()->getPost('texttopic', null);
            	if (!empty($value))
            	{
            		$data =  array();

            		if ($param===$TblTable->getTextField())
            		{
            			$data[$param]=urldecode($value);
            		}
            		else
            		{
            			$data[$param]=$value;
            		}
            		$this->model->update($this->_mainTable,$data,$idValue);
            		$this->queryTopics($idParent,$idUser);
            		//echo " Updating".$param.' with '.$value;
            	}
            	else
            	{
            		echo " EMpty Value for ".$param;
            	}
            }
            else
            {
            	echo " Empty param or idValue";
            }
            
		}
		else
		{
			echo " No identity";
		}

	}
	
}
?>
