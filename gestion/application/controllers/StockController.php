<?php
require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class StockController extends BaseController
{
	var $model;
	var $titleView;
	
	protected $_idkey = 'notodefine';
	protected $_controllername = 'stock';
	
    public function init()
    {
		$this->titleView='Gestion Existencias Stock';
		require_once(APPLICATION_PATH . '/models/ModelCatalogo.php');
		$this->model = new ModelCatalogo();
		
    }
    
    
    public function indexAction()
    {
   		$this->indexbaseAction();
    }
    
    public function indexsubaction()
    {
    	$this->titleView='Gestion Existencias Stock';
    	$this->view->tituloBusqueda = 'Existencias ultimos movimientos';
       	if($this->getRequest()->isPost())
       	{
    			$data = $this->getRequest()->getPost();
    			if ( isset($data['calidad']))
    			{
    			 	return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'stock' , 'action' => 'index', 'calidad' => $data['calidad'] ),
					'default', true);
    			}
    	}
    	else
    	{
    		$calidad = $this->getRequest()->getParam('calidad');
    		$this->view->columnas = array('calidad' => 'Calidad',
					'color' => 'Color',
					'tipoenvase' => 'TipoEnvase');
    		$this->view->locations = $this->model->queryViewLocations();
    		$this->view->columnas = array_merge((array)$this->view->columnas, (array) $this->view->locations);
    		
    		if ( isset($calidad))
    		{
    			$this->view->tituloBusqueda = 'Existencias Calidad['.$calidad.']';
    			
    			$this->view->existencias = $this->model->queryReferencias($calidad);
    		}
    		else
    		{
    			$this->view->existencias = $this->model->queryStockUltimosMovimientos();
    			$this->view->incorrectos = 	 $this->model->queryStockIncorrectos();
    			$this->view->totals = $this->model->queryTotals()->toArray();
    		}
   		}
    }
    
	
	public function createForm($tableid,$idValue,$fk,$fkvalue)
	{

		if ($tableid == 'idReferencia')
		{
			require_once(APPLICATION_PATH . '/forms/existencia.php');
			return new Form_Existencia();			
		}
	}
 
    
 	public function editexistenciaAction()
    {
    	$this->titleView='Gestion Existencias Stock';
    	$idReferencia = $this->getRequest()->getParam('idReferencia');
    	$ref = $this->model->queryID('TblReferencia',$idReferencia);
    	$this->view->titulo = 'Existencias Referencia ['.$ref['calidad'].'-'.$ref['color'].'-'.$ref['tipoenvase'].']';
    	$this->save('idReferencia','edit','index');
    }
    
  
    
}
?>