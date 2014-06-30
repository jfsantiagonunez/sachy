<?php
require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class DiaryController extends BaseController
{
	var $model;
	var $errorlogin;
	var $titleView;

	protected $_idkey = 'notodefine';
	protected $_controllername = 'Diary';
	protected $_mainTable='TblDiary';
	
	public function init()
	{
		/* Load DB_model */

		require_once(APPLICATION_PATH . '/models/ModelLeitmotif.php');
		$this->model = new ModelLeitmotif();
		$this->titleView = "Leitmotif Levels";
	}
	
	public function localQueryTopics($idParent,$idUser) {
		return $this->model->queryDiaryPerUser($idParent,$idUser);
	}





	
	
	
}
?>