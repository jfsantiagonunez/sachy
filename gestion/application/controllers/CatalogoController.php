<?php
require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class CatalogoController extends BaseController
{
	var $model;
	var $titleView;
	
	protected $_idkey = 'notodefine';
	protected $_controllername = 'catalogo';
	
    public function init()
    {
		$this->titleView='Gestion Catalogo Productos';
		require_once(APPLICATION_PATH . '/models/ModelCatalogo.php');
		$this->model = new ModelCatalogo();
		
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
    			if ( isset($data['calidad']))
    			{
    				return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'catalogo' , 'action' => 'index', 'calidad' => $data['calidad'] ),
					'default', true);
    			}
    	}
    	else
    	{
    		$calidad = $this->getRequest()->getParam('calidad');
    		if ( isset($calidad))
    		{
    			$this->view->calidades = $this->model->queryCalidades($calidad);
    			$this->view->referencias = $this->model->queryReferencias($calidad);
   			}
    		else
    		{
   				$this->view->calidades = $this->model->queryCalidades('');	
   			}
   		}
    
    }
    
	
	public function createForm($tableid,$idValue,$fk,$fkvalue)
	{
		if ($tableid == 'calidad')
		{
			require_once(APPLICATION_PATH . '/forms/calidad.php');
			return new Form_Calidad();
		}
		else if ($tableid == 'idReferencia')
		{
			require_once(APPLICATION_PATH . '/forms/referencia.php');
			return new Form_Referencia();			
		}
		else if ($tableid == 'idMarca')
		{
			require_once(APPLICATION_PATH . '/forms/marca.php');
			return new Form_Marca();			
		}
		else if ($tableid == 'idCategoria')
		{
			require_once(APPLICATION_PATH . '/forms/categoria.php');
			return new Form_Categoria();			
		}
	}
 
    public function editcalidadAction()
    {
    	$this->save('calidad','edit','index');
    }
    
    public function addcalidadAction()
    {
    	$this->save('calidad','add','index');
    }
    
	public function deletecalidadAction()
    {
    	$this->delete('calidad','index');
    }
    
 	public function editreferenciaAction()
    {
    	$this->save('idReferencia','edit','index');
    }
    
    public function addreferenciaAction()
    {
    	$this->save('idReferencia','add','index');
    }
    
	public function deletereferenciaAction()
    {
    	$this->delete('idReferencia','index');
    }
    
    public function indexmarcaycategoriaAction()
    {
  		$this->view->titleView = 'Gestion Marca y Categoria Productos';
    	$auth = Zend_Auth::getInstance();
    	if($auth->hasIdentity()) 
    	{
			$this->view->marcas = $this->model->queryAll('TblMarca');
			$this->view->categorias = $this->model->queryAll('TblCategoria');
    	}
    	else
    	{
    		return $this->_helper->redirector->gotoRoute(array(
							'controller' => 'index', 'action' => 'index'),
							'default', true );
    	}
    	
    }
    
	public function editmarcaAction()
    {
    	$this->save('idMarca','edit','indexmarcaycategoria');
    }
    
    public function addmarcaAction()
    {
    	$this->save('idMarca','add','indexmarcaycategoria');
    }
    
	public function deletemarcaAction()
    {
    	$this->delete('idMarca','indexmarcaycategoria');
    }
    
	public function editcategoriaAction()
    {
    	$this->save('idCategoria','edit','indexmarcaycategoria');
    }
    
    public function addcategoriaAction()
    {
    	$this->save('idCategoria','add','indexmarcaycategoria');
    }
    
	public function deletecategoriaAction()
    {
    	$this->delete('idCategoria','indexmarcaycategoria');
    }
}
?>