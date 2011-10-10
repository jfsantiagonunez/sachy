<?php
require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class CatalogueController extends BaseController
{
	var $model;
	var $titleView;
	
	protected $_idkey = 'notodefine';
	protected $_controllername = 'catalogue';
	
    public function init()
    {
		$this->titleView='Wine Catalogue';
		require_once(APPLICATION_PATH . '/models/ModelWine.php');
		$this->model = new ModelWine();
		
    }
    
    
    public function indexAction()
    {
   		$this->indexbaseAction();
    }
    
    public function indexsubaction()
    {
       	if($this->getRequest()->isPost())
       	{
    			$data = $this->getRequest()->getPost();
    			if ( isset($data['searchwine']))
    			{
    				return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'catalogue' , 'action' => 'index', 'searchwine' => $data['searchwine'] ),
					'default', true);
    				
    			}else{
    				return $this->_helper->redirector->gotoRoute(array(
    									'controller' => 'catalogue' , 'action' => 'index' ),
    									'default', true);
    			}
    	}
    	else
    	{
    		$searchwine = $this->getRequest()->getParam('searchwine');

    		$this->view->wines = $this->model->queryWines($searchwine);
   		
   		}
    
    }
    
    public function choosewineAction()
    {
 		if ($this->getRequest()->isPost())
 		{
 			$this->view->query='0';
 			$this->view->resultado= 'Tomate uno del Bierzo';
 		}else {
 			$this->view->query='1';
   			require_once(APPLICATION_PATH . '/forms/chosewine.php');
    		$this->view->form = new Form_ChooseWine();
 		}
    }
	
	public function createForm($tableid,$idValue,$fk,$fkvalue)
	{
		if ($tableid == 'wineId')
		{
			require_once(APPLICATION_PATH . '/forms/wine.php');
			return new Form_Wine();
		}

	}
	
	
 
    public function editwineAction()
    {
    	$this->save('wineId','edit','index');
    }
    
    public function addwineAction()
    {
    	$this->save('wineId','add','index');
    }
    
	public function deletewineAction()
    {
    	$this->delete('wineId','index');
    }

    public function importAction()
    {
    	//$this->model->importWine(array('winename'=>'Catena Alta Malbec','region'=>'Mendoza','winetype'=>'0','labellink'=>'Catena_Alta_Malbec'));
    	//$this->model->importWine(array('winename'=>'Alamos Selecci—n Pinot Noir','region'=>'Mendoza','winetype'=>'0','labellink'=>'Alamos_Seleccion_Pinot_Noir'));
    	//$this->model->importWine(array('winename'=>'Alamos Malbec','region'=>'Mendoza','winetype'=>'0','labellink'=>'Alamos_Malbec'));
    	//$this->model->importWine(array(	'winename'=>'Argento Malbec RosŽ','region'=>'Mendoza','winetype'=>'2','labellink'=>'Argento_Malbec_Rose'));
    	$this->model->importWine(array('winename'=>'Penfolds Rawson Retreat Semillon-Chardonnay','region'=>'South Eastern Australia','winetype'=>'1','labellink'=>'Penfolds_Rawson_Retreat_Shiraz_Cabernet_Sauvignon'));
    	$this->model->importWine(array('winename'=>'Penfolds Rawson Retreat Shiraz-Cabernet Sauvignon','region'=>'South Eastern Australia','winetype'=>'0','labellink'=>'Penfolds_Rawson_Retreat_Shiraz_Cabernet _Sauvignon'));
    	$this->model->importWine(array('winename'=>'Penfolds Rawson Retreat Riesling','region'=>'South Eastern Australia','winetype'=>'1','labellink'=>'Penfolds_Rawsons_Retreat_Riesling'));
    	$this->model->importWine(array('winename'=>'Shingleback Chardonnay','region'=>'Australia','winetype'=>'1','labellink'=>'Shingleback'));
    	$this->model->importWine(array('winename'=>'Nugan Pinot Noir','region'=>'Australia','winetype'=>'0','labellink'=>'Nugan_Pinot_Noir'));
    	$this->model->importWine(array('winename'=>'Peter Lehmann Future Shiraz','region'=>'Australia','winetype'=>'0','labellink'=>'Peter_Lehmann_Future_Shiraz'));
    	$this->model->importWine(array('winename'=>'Peter Lehmann Weighbridge Cabernet Sauvignon-Merlot','region'=>'Australia','winetype'=>'0','labellink'=>'Peter_Lehmann_Weighbridge_Cabernet'));
    	$this->model->importWine(array('winename'=>'Era Costana Tinto','region'=>'Rioja','winetype'=>'0','labellink'=>'Era_Costana_Tinto'));
    	
    	$this->_redirect('/catalogue/index');
    }
    
}