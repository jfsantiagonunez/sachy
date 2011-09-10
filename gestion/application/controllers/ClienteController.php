<?php

require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class ClienteController extends BaseController
{
	var $model;
	var $modelAlbaran;
	var $titleView;
	protected $_idkey = 'idCliente';
	protected $_controllername = 'cliente';
	
    public function init()
    {

		$this->titleView='Gestion Clientes';
		require_once(APPLICATION_PATH . '/models/ModelCliente.php');
		$this->model = new ModelCliente();
		
		require_once(APPLICATION_PATH . '/models/ModelAlbaran.php');
		$this->modelAlbaran = new ModelAlbaran();
    }

    
    public function indexAction()
    {
   		$this->indexbaseAction();
    }
    
    public function indexsubaction()
    {   
    	$this->titleView='Gestion Clientes';
    	$tableid = $this->model->getTableId();
    	
       	if($this->getRequest()->isPost())
       	{
       		
    		$data = $this->getRequest()->getPost();
    		if ( isset($data['cliente']))
    		{
    			return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'cliente' , 'action' => 'index', 'cliente' => $data['cliente'] ),
					'default', true);
    		}
    	}
    	else
    	{
    		$value = $this->getRequest()->getParam('cliente');
    	   	if ( isset($value))
    		{
    			$this->view->tituloBusqueda = 'Clientes que coinciden con ['.$value.']';
    			$this->view->clientes = $this->model->query($tableid,$value);
    			return;
    		}
    		
    		$value = $this->getRequest()->getParam('idReferencia');
    	   	if ( isset($value))
    		{
    			$this->view->consumo = '1';
    			$ref = $this->modelAlbaran->queryID('TblReferencia',$value)->toArray();
    			$this->view->tituloBusqueda = 'Clientes que consumen ['.$ref['calidad'].'-'.$ref['color'].'-'.$ref['tipoenvase'].'](unidades) en '.date('Y');
    			$this->view->clientes = $this->modelAlbaran->queryReferenciasClientes($value);
    			return;
    		}
    		$this->view->tituloBusqueda = 'Todos Clientes';
			$this->view->clientes = $this->model->queryAll($tableid);
    		
    	}
    }
    
	
	public function createForm($idkey,$idValue,$fk,$fkvalue)
	{
		if ($idkey == 'idCliente')
		{
			require_once(APPLICATION_PATH . '/forms/cliente.php');
			$form = new Form_Cliente();
			$form->initWith($idValue);
			return $form;
		}
		else if ($idkey == 'idDescuento')
		{
			require_once(APPLICATION_PATH . '/forms/descuento.php');
			return new Form_Descuento();
		}
		
	}
	
	public function indexdescuentoAction()
	{

		$value = $this->getRequest()->getParam('idCliente');
		if (isset($value))
		{
			$this->view->idCliente = $value;
			$cliente = $this->model->queryID('TblCliente',$value);
			$this->view->titleView='Gestion Descuentos Cliente <br/>[' . $cliente['nombre'] .']';
			$this->view->descuentos = $this->model->queryFK('TblDescuento',$value);
		}
	}
	
	public function adddescuentoAction()
	{
		$idCliente = $this->getRequest()->getParam('idCliente');
		if (isset($idCliente))
		{
			$this->save('idDescuento','add','indexdescuento','idCliente',$idCliente);
		}
		else
		{
			echo 'NO CLIENTE';
		}
	}
	
	public function editdescuentoAction()
	{
		$idCliente = $this->getRequest()->getParam('idCliente');
		if (isset($idCliente))
		{
			$this->save('idDescuento','edit','indexdescuento','idCliente',$idCliente);
		}
		else
		{
			echo 'NO CLIENTE';
		}
	}
    
    
}
?>