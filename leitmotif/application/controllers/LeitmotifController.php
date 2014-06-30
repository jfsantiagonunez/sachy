<?php
require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class LeitmotifController extends BaseController
{
	var $model;
	var $errorlogin;
	var $titleView;

	protected $_idkey = 'notodefine';
	protected $_controllername = 'Leitmotif';
	protected $_mainTable='TblLeitmotif';

	public function init()
	{
		/* Load DB_model */

		require_once(APPLICATION_PATH . '/models/ModelLeitmotif.php');
		$this->model = new ModelLeitmotif();
		$this->titleView = "Leitmotif Levels";
	}


	public function localQueryTopics($idParent,$idUser) {
		return $this->model->queryLeitmotifPerUser($idParent,$idUser);
	}
	


	public function testAction()
	{
		//$this->addTest("Blabal");
		$this->updatetest();
	}

	public function addTest($value)
	{
		$data = array();
		$auth = Zend_Auth::getInstance();
		$data['idUser'] = $auth->getIdentity()->idUser;
		$data['textleitmotif']=$value;
		$this->model->createLeitmotif($data);
	}

	
	
//	public function updatetest()
//	{
//		$this->disableLayout();
//		$auth = Zend_Auth::getInstance();
//		if($auth->hasIdentity())
//		{
//			 
//			$idParent = $this->getRequest()->getParam('idParent');
//            $idUser = $auth->getIdentity()->idUser;
//			 
//            $idValue = $this->getRequest()->getParam('idLeitmotif');
//            $param = $this->getRequest()->getParam('param');
//            if ( isset($idValue) && isset($param) )
//            {
//            	$value= "Leitmotif Web Chuchi";
//            	if (!empty($value))
//            	{
//            		$data =  array();
//            		//$data['idUser'] = $idUser;
//            		//$data['idParent']=$idParent;
//            		//$data['idLeitmotif']=$idValue;
//            		if ($param==='leitmotif')
//            		{
//            			$data[$param]=urldecode($value);
//            		}
//            		else
//            		{
//            			$data[$param]=$value;
//            		}
//            		$this->model->update('TblLeitmotif',$data,$idValue);
//            	}
//            }
//            //$this->queryLeitmotifs($idParent,$idUser);
//		}
//
//	}
}
?>