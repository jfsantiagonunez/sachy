<?php


require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class PruebaController extends BaseController
{

	var $titleView;
	protected $_idkey = 'calidad';
	protected $_controllername = 'Prueba';
	
	// Experiment
	protected $_form;
	
    public function init()
    {

		$this->titleView='Prueba';	
		//require_once(APPLICATION_PATH . '/models/ModelCatalogo.php');
		//$this->model = new ModelCatalogo();	
		require_once(APPLICATION_PATH . '/models/ModelAlbaran.php');
		$this->model = new ModelAlbaran();	
		$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('list', 'html')
                ->addActionContext('modify', 'html')
                ->initContext();
		
    }

    
    public function indexAction()
    {
    	require_once(APPLICATION_PATH . '/forms/movimientoestricto.php');

			$done = $this->getRequest()->getParam('Agregar');
			if (isset($done) && ($done=='Agregar'))
			{
				$data = $this->getRequest()->getParams();
				print_r($data);
				return;
			}
			else
			{
				$this->view->form = new Form_MovimientoEstricto();
			}
			$this->view->paramspartial = array();
			
    		$this->view->paramspartial['idKey'] = 'idAlbaran';
    		$this->view->paramspartial['idValue'] = '3573';
    		$this->view->paramspartial['controller'] = 'albaran';
			$this->view->paramspartial['idAlbaran'] = '3573';
			//$this->view->movimientos=$this->modelAlbaran->queryMovimientos('idAlbaran','3573');
			//$this->view->paramspartial['movimientos'] = $this->modelAlbaran->queryMovimientos('idAlbaran','3573');
			$this->view->idAlbaran='3573';
			$data['idAlbaran']='3573';
			$this->listmovimientosreturn($data);
    }
	
   public function listAction()
   {
   	    	$this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
   		$done = $this->getRequest()->getParam('Agregar');
		if (isset($done) && ($done=='Agregar'))
		{
			$data = $this->getRequest()->getParams();
			$data['idAlbaran']='3573';
			$this->modelAlbaran->createMovimiento($data);

		}
		
   }
    
	

    //Experiment

    
}
?>