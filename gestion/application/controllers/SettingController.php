<?php

require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class SettingController extends BaseController
{
	var $model;
	var $modelCatalogo;
	var $titleView;
	protected $_idkey = 'idKey';
	protected $_controllername = 'Setting';
	
	// Experiment
	protected $_form;
	
    public function init()
    {

		$this->titleView='Gestion Setting';
		require_once(APPLICATION_PATH . '/models/ModelSetting.php');
		$this->model = new ModelSetting();
		

    }

    
    
    
    public function indexAction()
    {
   		$this->indexbaseAction();
    }
    
    public function indexsubaction()
    {   
    	$this->titleView='Gestion Setting';
    	$tableid = $this->model->getTableId();    	
		$this->view->settings = $this->model->queryAll($tableid);	


    }
    
	public function createForm($idkey,$idValue,$fk,$fkvalue)
	{

		require_once(APPLICATION_PATH . '/forms/setting.php');
		return new Form_Setting();
	}
	   
	

    
}
?>