<?php

require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class AlbaranController extends BaseController
{
	var $model;
	var $modelStockLocation;
	var $modelCatalogo;
	var $modelCliente;
	var $titleView;
	var $defaultDeposito;
	protected $_idkey = 'idAlbaran';
	protected $_controllername = 'albaran';
	protected $_flashMessenger = null;
	
    public function init()
    {

		$this->titleView='Gestion Albaranes';
		require_once(APPLICATION_PATH . '/models/ModelAlbaran.php');
		$this->model = new ModelAlbaran();
		require_once(APPLICATION_PATH . '/models/ModelCatalogo.php');
		$this->modelCatalogo = new ModelCatalogo();
		//$this->modelStock = new ModelStock();
		require_once(APPLICATION_PATH . '/models/ModelCliente.php');
		$this->modelCliente = new ModelCliente();
		
		$auth  = Zend_Auth::getInstance(); 
    	if($auth->hasIdentity())
    	{
    		$this->defaultDeposito = $auth->getIdentity()->idDeposito;
    	}
    	$this->_flashMessenger =
            $this->_helper->getHelper('FlashMessenger');
        $this->initView();
    }
    
    public function indexsubaction()
    {   
    	$this->titleView='Gestion Albaranes'; 	
    	$this->view->tituloBusqueda = 'Resultado busqueda : ';
    	$depositos =  $this->modelCatalogo->queryAll('idDeposito')->toArray();
    	$this->view->messages = $this->_flashMessenger->getMessages();
    	$this->view->selectTienda = $this->generateSelect( 'idDeposito','idDeposito' , $depositos , $this->defaultDeposito, 'tienda');
    	$this->view->selectTiendaEntrada = $this->generateSelect( 'idDepositoEntrada','idDeposito' , $depositos , $this->defaultDeposito, 'tienda');
    	
    	$tipos = array('0'=>'salida','1'=>'entrada');  	
    	$this->view->selectEntrada = $this->generateSelect( 'entrada','entrada' ,$tipos , '0' );
    	//require_once(APPLICATION_PATH . '/forms/albaran.php');
    	//$this->view->formalbaran = new Form_Albaran();
    	
       	if($this->getRequest()->isPost())
       	{
       		
    		$data = $this->getRequest()->getPost();
    		
    		if (isset($data['busqueda']))
    		{
    			$value = $data['busqueda'];
    			if (isset($value) && ( $value != 'Numero Albaran o Nombre Cliente'))
    			{
    				return $this->_helper->redirector->gotoRoute(array(
						'controller' => 'albaran' , 'action' => 'index', 'busqueda' => $data['busqueda'] ),
						'default', true); 	
    			}
    		}
    		
    		
       		if (isset($data['cliente']))
			{
				$this->view->entrada = $data['entrada'];
       			$this->view->idDeposito = $data['idDeposito'];
       		
				// Viene de Nuevo Albaran en Albaran/Index
    			$this->view->clientes = $this->modelCliente->query($this->modelCliente->getTableId(),$data['cliente']);
    		
    			$countclientes = count($this->view->clientes);
    			if ( ( $countclientes == 0 ) || (!isset($this->view->clientes)))
    			{
    				$this->view->subtitleView = 'Cliente No encontrado. Refina tu busqueda';
    			} 
    			else if ($countclientes > 1 )
    			{
    				$this->view->subtitleView = 'Muchos clientes encontrados. Selecciona uno';
    			}
    			else if ($countclientes == 1 )
    			{
    				$this->view->cliente = $this->view->clientes[0];
    				$idCliente = $this->view->cliente['idCliente'];
    				$datalbaran = array('idCliente'=>$idCliente,'entrada'=>$data['entrada'],'idDeposito'=>$data['idDeposito']);
    				return $this->generateAlbaran($datalbaran);				
    				
    			}
    			return;
			}
				
    		
    	}
    	else
    	{
    		$value = $this->getRequest()->getParam('busqueda');
    		if (isset($value) && ( $value != 'Numero Albaran o Nombre Cliente'))
    		{
    			$this->view->tituloBusqueda .=  ' [' . $value .']';
    			$this->view->albaranes = $this->model->queryAlbaran('nombre',$value);
    			$morealbaranes = $this->model->queryAlbaran('numeroalbaran',$value);
    			$position = count($this->view->albaranes);
    			$numeroalbaranes = count($morealbaranes);
    			
    			if ( (  $position < 1 ) && ( $numeroalbaranes > 0 ) )
    			{
    				$this->view->albaranes = $morealbaranes;
    			}
    			else if ( $numeroalbaranes > 0 )
    			{	
    				foreach( $morealbaranes as $row )
    				{
    					$this->view->albaranes[$position] = $row;
    					$position +=1;
    				} 
    			}
    			return; 	
    		}
    		
    		$value = $this->getRequest()->getParam('idAlbaran');
    	   	if ( isset($value))
    		{
    			$this->view->albaranes = $this->model->queryAlbaran('idAlbaran',$value);
    			return;
    		}
    		
    		$value = $this->getRequest()->getParam('idCliente');
    		if ( isset($value))
    		{
    			$cliente = $this->modelCliente->queryID('TblCliente',$value);
    			$this->view->tituloBusqueda = 'Albaranes por Cliente ['.$cliente['nombre'].']';
    			$this->view->albaranes = $this->model->queryAlbaran('idCliente',$value);
    			return;
    		}
    		
    		$value = $this->getRequest()->getParam('idFactura');
    		if ( isset($value))
    		{
    			$this->view->albaranes = $this->model->queryAlbaran('idFactura',$value);
    			return;
    		}
    		
    		$value = $this->getRequest()->getParam('numeroalbaran');
    		if ( isset($value))
    		{
    			$this->view->albaranes = $this->model->queryAlbaran('numeroalbaran',$value);
    			return;
    		}
    		
    		$value = $this->getRequest()->getParam('idReferencia');
    		if ( isset($value))
    		{
    			$ref = $this->model->queryID('TblReferencia',$value)->toArray();
    			$this->view->tituloBusqueda = 'Albaranes con referencia ['.$ref['calidad'].'-'.$ref['color'].'-'.$ref['tipoenvase'].'](unidades) en '.date('Y');
    			$this->view->albaranes = $this->model->queryAlbaranPorReferencias($value);
    			return;
    		}
    		$this->view->tituloBusqueda = 'Ultimos Albaranes';
			$this->view->albaranes = $this->model->queryUltimosAlbaranes();
    	}
    }
    
	
	public function createForm($tableid,$idValue,$fk,$fkvalue)
	{
		if ($tableid == 'idMovimiento')
		{
			require_once(APPLICATION_PATH . '/forms/movimientofactura.php');
			$form = new Form_MovimientoFactura();
			
			$albaran = $this->model->queryID('TblAlbaran',$fkvalue);
			$elemS = $form->getElement('salida');
			
			$list2 = $this->modelCliente->queryToDisplayValues('TblDescuento',$albaran['idCliente']);
			$list2['0'] = '0';
			$list2['100'] = '100';

			$elemD = $form->getElement('descuento');
			
			$elemD->setOptions(array(
					'label' => 'Descuento',
					'multiOptions'             => $list2,
    				'required'                 => true,
    				'registerInArrayValidator' => false));
			
			
			$idMovimiento = $this->getRequest()->getParam('idMovimiento');
			
			if (isset($idMovimiento))
			{
				// Esto es un edit
				$mov = $this->model->getTable('TblMovimiento')->selectId($idMovimiento);
				$ref = $this->model->getTable('TblReferencia')->selectId($mov['idReferencia']);
				$this->view->titulo = 'Modificar Movimiento ['.$ref['calidad'].'-'.$ref['color'].'-'.$ref['tipoenvase'].']';
				$elemD->setValue($mov['descuento']);
				$form->removeElement('calidad');
				$form->removeElement('color');
				$form->removeElement('tipoenvase');
				
			}
			else
			{
				// Esto es un adicion
				$value = '1';
				if ($albaran['entrada'] == '1')
					$value = '-1';
				$elemS->setValue($value);
								
				$value = $this->modelCliente->getDefaultDescuento($albaran['idCliente']);
				$elemD->setValue($value);
			
			}
			
			return $form;
		}
	}
	
	public function editAction()
	{
		$idAlbaran = $this->getRequest()->getParam('idAlbaran');
		return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'albaran' , 'action' => 'nuevoalbaran', 'idAlbaran' => $idAlbaran ),
					'default', true);
	}
	
	public function nuevoalbaranAction2()
	{
		$this->view->titleView='Creando Albaran';
		$this->view->subtitleView = 'Introducir Referencias';


		if($this->getRequest()->isPost())
       	{
       		$data = $this->getRequest()->getPost();
    		
			// Determinar siguiente paso en funcion del numero de clientes encontrados.
			
			if (isset($data['cliente']))
			{
				$this->view->entrada = $data['entrada'];
       			$this->view->idDeposito = $data['idDeposito'];
       		
				// Viene de Nuevo Albaran en Albaran/Index
    			$this->view->clientes = $this->modelCliente->query($this->modelCliente->getTableId(),$data['cliente']);
    		
    			$countclientes = count($this->view->clientes);
    			if ( ( $countclientes == 0 ) || (!isset($this->view->clientes)))
    			{
    				$this->view->subtitleView = 'Cliente No encontrado. Refina tu busqueda';
    			} 
    			else if ($countclientes > 1 )
    			{
    				$this->view->subtitleView = 'Muchos clientes encontrados. Selecciona uno';
    			}
    			else if ($countclientes == 1 )
    			{
    				$this->view->cliente = $this->view->clientes[0];
    				$idCliente = $this->view->cliente['idCliente'];
    				$datalbaran = array('idCliente'=>$idCliente,'entrada'=>$data['entrada'],'idDeposito'=>$data['idDeposito']);
    				return $this->generateAlbaran($datalbaran);
    				
    			}
    			return;
			}
       	}
       	else
       	{
       		
       		// Get movimientos
       		$idAlbaran = $this->getRequest()->getParam('idAlbaran');
       		
       		$albaran = $this->model->queryID('TblAlbaran',$idAlbaran);
       		$this->view->cliente = $this->modelCliente->queryID($this->modelCliente->getTableId(),$albaran['idCliente']);
       		$this->view->albaran = $albaran ;
       		$this->view->entradatext = 'salida';
       		if ( $albaran['entrada'] == '1' )
       			$this->view->entradatext = 'entrada';
       		$this->view->tienda = $this->modelCatalogo->getTienda($albaran['idDeposito']);
       		$this->view->movimientos = $this->model->queryMovimientos('idAlbaran',$idAlbaran);
       	}
	}
    
	public function nuevoalbaranAction()
	{
		$this->view->titleView='Creando Albaran';
		$this->view->subtitleView = 'Introducir Referencias';


		if($this->getRequest()->isPost())
       	{
       		$data = $this->getRequest()->getPost();
    		
			// Determinar siguiente paso en funcion del numero de clientes encontrados.
			
			if (isset($data['cliente']))
			{
				$this->view->entrada = $data['entrada'];
       			$this->view->idDeposito = $data['idDeposito'];
       		
				// Viene de Nuevo Albaran en Albaran/Index
    			$this->view->clientes = $this->modelCliente->query($this->modelCliente->getTableId(),$data['cliente']);
    		
    			$countclientes = count($this->view->clientes);
    			if ( ( $countclientes == 0 ) || (!isset($this->view->clientes)))
    			{
    				$this->view->subtitleView = 'Cliente No encontrado. Refina tu busqueda';
    			} 
    			else if ($countclientes > 1 )
    			{
    				$this->view->subtitleView = 'Muchos clientes encontrados. Selecciona uno';
    			}
    			else if ($countclientes == 1 )
    			{
    				$this->view->cliente = $this->view->clientes[0];
    				$idCliente = $this->view->cliente['idCliente'];
    				$datalbaran = array('idCliente'=>$idCliente,'entrada'=>$data['entrada'],'idDeposito'=>$data['idDeposito']);
    				return $this->generateAlbaran($datalbaran);
    				
    			}
    			return;
			}
       	}
       	else
       	{
       		$done = $this->getRequest()->getParam('Agregar');
			if (isset($done) && ($done=='Agregar'))
			{
				//This is handle somewhere else. See stock/nuevomovimiento.phtml
				return;
			}
	
			// Get movimientos
       		$idAlbaran = $this->getRequest()->getParam('idAlbaran');
         	$albaran = $this->model->queryID('TblAlbaran',$idAlbaran);
       		$this->view->cliente = $this->modelCliente->queryID($this->modelCliente->getTableId(),$albaran['idCliente']);
       		$this->view->albaran = $albaran ;
       		$this->view->idAlbaran=$idAlbaran;
       		$this->view->entradatext = 'salida';
       		if ( $albaran['entrada'] == '1' )
       			$this->view->entradatext = 'entrada';  

       		$this->view->tienda = $this->modelCatalogo->getTienda($albaran['idDeposito']);
       		$this->view->movimientos = $this->model->queryMovimientos('idAlbaran',$idAlbaran);
       		
			$this->view->paramspartial = array();		
    		$this->view->paramspartial['idKey'] = 'idAlbaran';
    		$this->view->paramspartial['idValue'] = $idAlbaran;
    		$this->view->paramspartial['controller'] = 'albaran';
    		$this->view->paramspartial['entrada'] = $albaran['entrada'];
    		$this->view->paramspartial['idCliente'] = $albaran['idCliente'];

			$this->listmovimientosreturn($albaran->toArray());
			       		
       	}
	}
	public function elegirclienteAction()
	{
		$idCliente = $this->getRequest()->getParam('idCliente');
		$entrada = $this->getRequest()->getParam('entrada');
		$idDeposito = $this->getRequest()->getParam('idDeposito');
		$datalbaran = array('idCliente'=>$idCliente,'entrada'=>$entrada,'idDeposito'=>$idDeposito);
		return $this->generateAlbaran($datalbaran);
	}
	
	public function generateAlbaran($datalbaran)
	{			
    	$albaran = $this->model->createAlbaran($datalbaran);
    	
    	if (isset($albaran))
    	{
    		return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'albaran' , 'action' => 'nuevoalbaran', 'idAlbaran' => $albaran ),
					'default', true);
    	}
	}
	
	public function addmovimientoAction()
	{
		$idAlbaran = $this->getRequest()->getParam('idAlbaran');
		if (isset($idAlbaran))
		{
			$this->save('idMovimiento','add','nuevoalbaran','idAlbaran',$idAlbaran);
		}
		else
		{
			echo 'NO ALBARAN';
		}
	}
	
	public function editmovimientoAction()
	{
		$idAlbaran = $this->getRequest()->getParam('idAlbaran');
		if (isset($idAlbaran))
		{
			$this->save('idMovimiento','edit','nuevoalbaran','idAlbaran',$idAlbaran);
		}
		else
		{
			echo 'NO ALBARAN';
		}
	}
	
	public function deletemovimientoAction()
	{
		$idAlbaran = $this->getRequest()->getParam('idAlbaran');
		if (isset($idAlbaran))
		{
			$this->delete('idMovimiento','nuevoalbaran','idAlbaran',$idAlbaran);
		}
		else
		{
			echo 'NO ALBARAN';
		}
	}
	
	public function finalizardocumentoAction()
	{
		$finaldocumento = $this->getRequest()->getParam('finaldocumento');
		switch ($finaldocumento)
		{
			case 'Salir':
				$this->salirAction();
				break;
			case 'Solo Guardar':
				return $this->gestionaralbaran(true,false);
				break;
			case 'Volver a Imprimir':
				return $this->gestionaralbaran(false,true);
				break;
			case 'Facturar':
				$idAlbaran = $this->getRequest()->getParam('idAlbaran');
				return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'factura' , 'action' => 'elegiralbaran' , 'idAlbaran' => $idAlbaran ),
					'default', true);
				break;
			case 'Imprimir & Guardar':
				return $this->gestionaralbaran(true,true);
				break;
		}	
	}

	public function salirAction()
	{
		return  $this->_helper->redirector->gotoRoute(array(
					'controller' => 'albaran' , 'action' => 'index' ),
					'default', true);
	}
	
	public function gestionaralbaran($contabilizarstock,$imprimir)
	{
		$idAlbaran = $this->getRequest()->getParam('idAlbaran');
		
		if (isset($idAlbaran))
		{
			$albaran = $this->model->queryID('TblAlbaran',$idAlbaran);
			
			// Chequear estado
			$estado = $albaran['estado'];
			
			// Si el estado no es terminado, ignoramos la accion y lo registramos en el stock, ANYWAY
			if ($estado == '0')
			{
				$this->model->registrarStock($idAlbaran);
			}
			
			if ( $imprimir )
			{
				return $this->imprimirAlbaran($albaran);
			}
			
		}
		
		$this->salirAction();	
	}
	
	

	
	
   public function indexmovimientosAction()
    {   
    	$this->view->titleView='Gestion Movimientos'; 	
   		
    	$value = $this->getRequest()->getParam('idCliente');
    	if ( isset($value))
    	{
    		$cliente = $this->modelCliente->queryID('TblCliente',$value);
  			$this->view->tituloBusqueda = 'Movimientos por Cliente [' . $cliente['nombre'] .']';
  			
   			$this->view->movimientos = $this->model->querySumMovimientos('idCliente',$value);
   			//Zend_Debug::dump($this->view->movimientos);
   			return;
   		}
    		    	
    }
    
    
    public function transferenciaAction()
    {
       	if($this->getRequest()->isPost())
       	{	
    		$data = $this->getRequest()->getPost();
    		
    		if (isset($data['idDeposito'])&&isset($data['idDepositoEntrada']))
    		{
    			if ($data['idDeposito'] == $data['idDepositoEntrada'] )
    			{
    				$this->_flashMessenger->addMessage( 'Has elegido la misma entrada y salida');
    			}
    			else
    			{
    				$depot = $this->modelCatalogo->queryID('idDeposito',$data['idDepositoEntrada']);
    				$idCliente = $depot['idCliente'];
    				$this->view->cliente =  $this->modelCliente->queryID('TblCliente',$idCliente);
    				$datalbaran = array('idCliente'=>$idCliente,'entrada'=>'0','idDeposito'=>$data['idDeposito'],'tipotransfer'=>'1','facturable'=>'0');
    				
    				return $this->generateAlbaran($datalbaran);				
 
    			}
    		}
    		$this->salirAction();
       	}
       	
    }
    
 	public function transvaseAction()
    {
       	if($this->getRequest()->isPost())
       	{	
    		$data = $this->getRequest()->getPost();
    		
    		if (isset($data['idDeposito']))
    		{
   				$depot = $this->modelCatalogo->queryID('idDeposito',$data['idDeposito']);
    				$idCliente = $depot['idCliente'];
    				$this->view->cliente =  $this->modelCliente->queryID('TblCliente',$idCliente);
    				$datalbaran = array('idCliente'=>$idCliente,'entrada'=>'1','idDeposito'=>$data['idDeposito'],'tipotransfer'=>'2','facturable'=>'0');
    				
    				return $this->generateAlbaran($datalbaran);						
    		}
    		$this->salirAction();
       	}
       	
    }
    
    public function adddatatoobjectAction()
    {
    	$this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
    	$data = $this->getRequest()->getPost();
    	$idAlbaran = $idAlbaran = $this->getRequest()->getParam('idAlbaran');
    	if (isset($idAlbaran))
    	{
    		$albaran = $this->model->queryID('TblAlbaran',$idAlbaran);
    		if (isset($albaran))
    		{
    			$keys = array_keys($data);
    			foreach($keys as $key)
    			{
    			   $albaran[$key]=$data[$key];
    			}
    			$albaran->save();
    		}
    	}
    }

 
    
		public function imprimirAlbaran($albaran)
		{
			$this->_helper->layout->disableLayout();
			// Load Zend_Pdf class 
			require_once('Zend/Pdf.php'); 
			$pdf = new Zend_Pdf(); 

			// Add new page to the document 
			$page = $this->createPage($pdf,"595:800:");
			
			$cliente = $this->modelCliente->queryID('TblCliente',$albaran['idCliente']);
			$this->imprimirCabeceraAlbaran($albaran,$cliente,$page);
			$this->imprimirMovimientos($albaran,$cliente,'idAlbaran',$page,$pdf);
			//$this->imprimirPaginaReferencia($page);
			$this->view->pdfData = $pdf->render(); 
			$this->view->numeroalbaran = $albaran['numeroalbaran'];	
			
		}
		
		
		
		function imprimirCabeceraAlbaran($albaran,$cliente,$page)
		{
			$thy = 740;  //fecha, numero
			$thx = 425;  //fecha, nif, telefono
			$thy2 = 605; // nombre, nif		
			$thx2= 25;   //nombre,domicilio
			
			$thx3 = 185 ; //comentario, orden cliente
			$thy3 = 535; // orden cliente
			$lh = 12;
			$date = new Zend_Date($albaran['fecha']);
			$dateprint= $date->toString('dd-MM-YYYY');
			$page->drawText($dateprint, $thx, $thy, 'UTF-8');
			$page->drawText($albaran['numeroalbaran'], $thx + 85, $thy, 'UTF-8');
			
			$page->drawText($cliente['nombre'], $thx2, $thy2, 'UTF-8'); 
			$page->drawText($cliente['nif'], $thx, $thy2, 'UTF-8');
			$page->drawText($cliente['direccion'] . ' - ('.$cliente['codigopostal'].') ' . $cliente['ciudad'], $thx2, $thy2-30, 'UTF-8');
			$page->drawText($cliente['telefono'], $thx, $thy2-30, 'UTF-8');
			if ($albaran['ordencliente']!='')
				$page->drawText('Orden Cliente:'. $albaran['ordencliente'], $thx3, $thy3, 'UTF-8'); 
			if ($cliente['numrefproveedor']!='')
				$page->drawText('Proveedor:'. $cliente['numrefproveedor'], $thx3, $thy3+150, 'UTF-8'); 
			$page->drawText( $albaran['comentario'], $thx3, $thy3-$lh, 'UTF-8');
		}

	
		
}
?>
