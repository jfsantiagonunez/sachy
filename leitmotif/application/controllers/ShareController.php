<?php
require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class ShareController extends BaseController
{
	var $model;
	var $errorlogin;
	var $titleView;

	protected $_idkey = 'notodefine';
	protected $_controllername = 'Share';
	protected $_mainTable='TblShare';

	public function init()
	{
		/* Load DB_model */

		require_once(APPLICATION_PATH . '/models/ModelLeitmotif.php');
		$this->model = new ModelLeitmotif();
		$this->titleView = "Leitmotif Levels";
	}


	public function localQueryTopics($idParent,$idUser) {
		$this->view->useDateInTitle = false;
		
		if ($idParent==='top') {
			$this->view->canaddnew=false;
			return $this->model->queryUsersSharingWithUser($idUser);
		} else {
			$this->view->controller='Leitmotif';
			$this->view->namePk = $this->model->getTable('TblLeitmotif')->getPK();
		    $this->view->textField = $this->model->getTable('TblLeitmotif')->getTextField();
			return $this->model->querySharedLeitmotifPerUser($idParent,$idUser);
		}
	}
	
	public function localQueryTopic($idParent)
	{
		// Do nothing
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

}
?>