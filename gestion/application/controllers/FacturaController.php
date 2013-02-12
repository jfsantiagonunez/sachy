<?php

require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class FacturaController extends BaseController
{
	var $model;
	var $modelStockLocation;
	var $modelCatalogo;
	var $modelCliente;
	var $titleView;
	protected $_idkey = 'idFactura';
	protected $_controllername = 'factura';
	
	
    public function init()
    {

		$this->titleView='Gestion Facturas';
		require_once(APPLICATION_PATH . '/models/ModelFactura.php');
		$this->model = new ModelFactura();
		require_once(APPLICATION_PATH . '/models/ModelCatalogo.php');
		$this->modelCatalogo = new ModelCatalogo();
		//$this->modelStock = new ModelStock();
		require_once(APPLICATION_PATH . '/models/ModelCliente.php');
		$this->modelCliente = new ModelCliente();
    }
    
    public function indexsubaction()
    {   
    	$this->titleView='Gestion Facturas'; 	
    	$this->view->tituloBusqueda = 'Resultado busqueda : ';
    	
     		
       	if($this->getRequest()->isPost())
       	{
       		
    		$data = $this->getRequest()->getPost();
    		
    		if (isset($data['busqueda']))
    		{
    			$value = $data['busqueda'];
    			if (isset($value) && ( $value != 'Numero factura o Nombre Cliente'))
    			{
    				return $this->_helper->redirector->gotoRoute(array(
						'controller' => 'factura' , 'action' => 'index', 'busqueda' => $data['busqueda'] ),
						'default', true); 	
    			}
    		}
       	    
    		if (isset($data['cliente']))
			{
      		
				
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
    				$datafactura = array('idCliente'=>$idCliente);
    				return $this->generatefacturaproxy($datafactura);
    				
    			}
    			return;
			}
      		
    	}
    	else
    	{
    		$value = $this->getRequest()->getParam('busqueda');
    		$this->view->imprimirId = $this->getRequest()->getParam('imprimirId');
    		if (isset($value) && ( $value != 'Numero factura o Nombre Cliente'))
    		{
    			$this->view->tituloBusqueda .=  ' [' . $value .']';
    			$this->view->facturas = $this->model->queryfactura('nombre',$value);
    			$moreFacturas = $this->model->queryfactura('numerofactura',$value);
    			$position = count($this->view->facturas);
    			$numeroFacturas = count($moreFacturas);
    			
    			if ( (  $position < 1 ) && ( $numeroFacturas > 0 ) )
    			{
    				$this->view->facturas = $moreFacturas;
    			}
    			else if ( $numeroFacturas > 0 )
    			{	
    				foreach( $moreFacturas as $row )
    				{
    					$this->view->facturas[$position] = $row;
    					$position +=1;
    				} 
    			}
    			return; 	
    		}
    		
    		$value = $this->getRequest()->getParam('idFactura');
    	   	if ( isset($value))
    		{
    			$this->view->facturas = $this->model->queryFactura('idFactura',$value);
    			return;
    		}
    		
    		$value = $this->getRequest()->getParam('idCliente');
    		if ( isset($value))
    		{
    			$cliente = $this->modelCliente->queryID('TblCliente',$value);
    			$this->view->tituloBusqueda = 'Facturas por Cliente ['.$cliente['nombre'].']';
    			$this->view->facturas = $this->model->queryFactura('idCliente',$value);
    			
    			$this->view->albaranespendientes = $this->model->queryAlbaranesPendientes('idCliente',$cliente['idCliente']);
    			return;
    		}
    		
    		$value = $this->getRequest()->getParam('idAlbaran');
    		if ( isset($value))
    		{
    			$this->view->facturas = $this->model->queryFacturaPorAlbaran($value);
    			return;
    		}
    		
    		$value = $this->getRequest()->getParam('numerofactura');
    		if ( isset($value))
    		{
    			$this->view->facturas = $this->model->queryFactura('numerofactura',$value);
    			return;
    		}
    		
 
    		$this->view->tituloBusqueda = 'Ultimos Facturas';
			$this->view->facturas = $this->model->queryUltimosFacturas();
			
			$this->view->albaranespendientes = $this->model->queryAlbaranesPendientes();
    	}
    }
    
	
	public function createForm($tableid,$idValue,$fk,$fkvalue)
	{
		if ($tableid == 'idMovimiento')
		{
			require_once(APPLICATION_PATH . '/forms/movimientofactura.php');
			$form = new Form_MovimientoFactura();
			
			$factura = $this->model->queryID('TblFactura',$fkvalue);
			$elemS = $form->getElement('salida');
			
			$list2 = $this->modelCliente->queryToDisplayValues('TblDescuento',$factura['idCliente']);
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
				if ($factura['abono'] == '1')
					$value = '-1';
				$elemS->setValue($value);
								
				$value = $this->modelCliente->getDefaultDescuento($factura['idCliente']);
				$elemD->setValue($value);
			
			}
			
			//$this->view->titulo = 'Modificar Movimiento ['.$ref['calidad'].'-'.$ref['color'].'-'.$ref['tipoenvase'].']';
						
			return $form;
		}
	}
	
	public function editAction()
	{
		$idFactura = $this->getRequest()->getParam('idFactura');
		return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'factura' , 'action' => 'nuevofactura', 'idFactura' => $idFactura ),
					'default', true);
	}
	
    
	
	public function nuevofacturaAction()
	{
		$this->view->titleView='Creando Factura';
		$this->view->subtitleView = 'Revisar Referencias';
		

       	// Get movimientos
       	$idFactura = $this->getRequest()->getParam('idFactura');
       	
       	$factura = $this->model->queryID('TblFactura',$idFactura);
       	$this->view->cliente = $this->modelCliente->queryID($this->modelCliente->getTableId(),$factura['idCliente']);
       	$this->view->factura = $factura ;

       	//$this->view->movimientos = $this->model->queryMovimientos('idFactura',$idFactura);
       	$this->view->albaranespendientes = $this->model->queryAlbaranesPendientes($factura['idCliente']);
       	$this->view->albarabesestafactura = $this->model->queryAlbaranesPorFactura($factura['idFactura']);
       	$this->view->descuentos = $this->modelCliente->queryFK('TblDescuento',$factura['idCliente']);
       	$this->view->idCliente = $factura['idCliente'];

       	$listtipopago = $this->model->getTipoPago();
		$vencimientos = $this->model->getDiasPago();
		
		$this->view->selectTipopago = $this->generateSelect( 'condicionespago','condicionespago' , $listtipopago , $factura['condicionespago']);
       	
		$this->view->paramspartial = array();		
    	$this->view->paramspartial['idKey'] = 'idFactura';
    	$this->view->paramspartial['idValue'] = $idFactura;
    	$this->view->paramspartial['controller'] = 'factura';
    	$this->view->paramspartial['entrada'] = '0';
    	$this->view->paramspartial['idCliente'] = $factura['idCliente'];
    	
		$this->view->listadodiv= $this->listmovimientosreturn($factura->toArray());
		
	}
	
	public function elegirclienteAction()
	{
		$idCliente = $this->getRequest()->getParam('idCliente');

		$datfactura = array('idCliente'=>$idCliente);
		return $this->generatefacturaproxy($datfactura);
	}
	
	public function elegiralbaranAction()
	{
		$idAlbaran = $this->getRequest()->getParam('idAlbaran');
		$albaran = $this->model->queryID('TblAlbaran',$idAlbaran);
		$datfactura = array('idAlbaran'=>$idAlbaran,
							'idCliente'=>$albaran['idCliente'],
							'descuentoaplicartotal'=>$albaran['descuentoaplicartotal']);
				
		return $this->generatefacturaproxy($datfactura,$albaran);
	}
	
	public function generatefacturaproxy($datfactura)
	{
		$idFactura = $this->model->generatefactura($datfactura);
		
		if ($idFactura > 0)
		{
    		return $this->_helper->redirector->gotoRoute(array(
			'controller' => 'factura' , 'action' => 'nuevofactura', 'idFactura' => $idFactura ),
			'default', true);
		}
	}
	
	
	public function asignarfacturaalbaranAction()
	{
		$idAlbaran = $this->getRequest()->getParam('idAlbaran');
		$idFactura = $this->getRequest()->getParam('idFactura');
		
		if (isset($idAlbaran) && isset($idFactura))
		{
			$this->model->asignarfacturaalbaran($idFactura,$idAlbaran);
		
			return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'factura' , 'action' => 'nuevofactura', 'idFactura' => $idFactura ),
					'default', true);
		}
		
	}
	
	
	
	public function desasignarfacturaalbaranAction()
	{
		$idAlbaran = $this->getRequest()->getParam('idAlbaran');
		$idFactura = $this->getRequest()->getParam('idFactura');
		$albaran = $this->model->queryID('TblAlbaran',$idAlbaran);
		$albaran['idFactura'] = null;	
		$albaran->save();
		
		// !!!! Desasignar facturas/descuentos de los movimientos
		$this->model->desasignarMovimientosConFactura($idAlbaran,$idFactura);

		return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'factura' , 'action' => 'nuevofactura', 'idFactura' => $idFactura ),
					'default', true);
	}
	
	
 	public function deleteAction()
    {
    	$idFactura = $this->getRequest()->getParam('idFactura');
		
    	// Get related albaranes
    	$albaranes = $this->model->getTable('TblAlbaran')->selectWhere('idFactura = \'' .$idFactura.'\'');
    	//echo 'Hola' . $idFactura;
    	foreach($albaranes as $albaran)
    	{	
    		//echo 'Deasignar albaran : '.$albaran['idAlbaran'];
    		$idAlbaran = $albaran['idAlbaran'];
    		$this->model->desasignarMovimientosConFactura($idAlbaran,$idFactura);

    		$albaran['idFactura'] = null;	
    		$albaran->save();
    	}
    	
		$this->model->delete('idFactura',$idFactura);
		
		return $this->retornardeaccion('factura', 'index'); 
    }
	
	

	
	
	public function addmovimientoAction()
	{
		$idFactura = $this->getRequest()->getParam('idFactura');
		if (isset($idFactura))
		{
			$this->save('idMovimiento','add','nuevofactura','idFactura',$idFactura);
		}
		else
		{
			echo 'NO factura';
		}
	}
	
	public function editmovimientoAction()
	{
		$idFactura = $this->getRequest()->getParam('idFactura');
		if (isset($idFactura))
		{
			$this->save('idMovimiento','edit','nuevofactura','idFactura',$idFactura);
		}
		else
		{
			echo 'NO factura';
		}
	}
	
	public function deletemovimientoAction()
	{
		$idFactura = $this->getRequest()->getParam('idFactura');
		$idMovimiento = $this->getRequest()->getParam('idMovimiento');
		if (isset($idFactura))
		{
			$mov = $this->model->getTable('TblMovimiento')->selectId($idMovimiento);
			if ( isset($mov['idAlbaran']) && ($mov['idAlbaran']!='0') )
			{
				echo 'alert("No puedes borrar movimiento asignado a un albaran.");';
				return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'factura' , 'action' => 'nuevofactura', 'idFactura' => $idFactura ),
					'default', true);
			}
			else
			{
				$this->delete('idMovimiento','nuevofactura','idFactura',$idFactura);
			}
		}
		else
		{
			echo 'NO factura';
		}
	}
	
	public function finalizardocumentoAction()
	{
		$finaldocumento = $this->getRequest()->getParam('finaldocumento');
		switch ($finaldocumento)
		{
			case 'Salir':				
				$this->salirAccion('factura');
				break;
			case 'Solo Guardar':
				return $this->gestionarfactura(true,false);
				break;
			case 'Volver a Imprimir':
				return $this->gestionarfactura(false,true);
				break;
			case 'Imprimir & Guardar':
			case 'Recalcular & Imprimir':
				return $this->gestionarfactura(true,true);
				break;
		}	
	}
	
		
	public function gestionarfactura($generariva,$imprimir)
	{
		$idFactura = $this->getRequest()->getParam('idFactura');
		
		if (isset($idFactura))
		{
			$factura = $this->model->queryID('TblFactura',$idFactura);
			
			// Chequear estado
			$estado = $factura['estado'];
			
			// Si el estado no es terminado, ignoramos la accion y lo registramos en el stock, ANYWAY
			if ( ($estado == '0') || ($generariva == true ) )
			{
				$factura['descuentoaplicartotal']=$this->getRequest()->getParam('descuento');
				$this->model->contabilizarObjeto('idFactura',$factura,$idFactura);
			}
			
			//if ( $imprimir )
			//{
			//	return $this->imprimirFactura($factura);
			//}
			
		}
		
		$this->salirAccion('factura',$imprimir,$idFactura);	
	}
	
	
	public function imprimirAction()
	{
		$idFactura = $this->getRequest()->getParam('idFactura');

		if (isset($idFactura))
		{
			$factura = $this->model->queryID('TblFactura',$idFactura);
			return $this->imprimirFactura($factura);
		}
		echo "NINGUNA FACTURA QUE IMPRIMIR";
		return ;
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
    
   	public function ignoraralbaranAction()
    {
    	$idAlbaran = $this->getRequest()->getParam('idAlbaran');
    	if (isset($idAlbaran))
    	{
    		$this->model->setalbaranfacturable($idAlbaran,'0');
    	}
    	$this->salirAccion('factura');
    }
    
    public function cambiopagofacturaAction()
    {
    	$idFactura = $this->getRequest()->getParam('idFactura');
    	
    	$factura = $this->model->queryID('TblFactura',$idFactura);
    	
    	$data = $this->getRequest()->getPost();
    	
    	$factura['fecha'] = $data['fecha'];
    	$factura['condicionespago'] = $data['condicionespago'];
    	if (isset($data['vencimiento']))
    	{
    		$factura['vencimiento'] = $data['vencimiento'];
    	}
    	$factura->save();
    	
    	return  $this->_helper->redirector->gotoRoute(array(
					'controller' => 'factura' , 'action' => 'nuevofactura' , 'idFactura' => $idFactura ),
					'default', true);
    	
    }
    
	public function imprimirFactura($documento)
	{
		// Load Zend_Pdf class 
		$this->_helper->layout->disableLayout();
		require_once('Zend/Pdf.php'); 
		$pdf = new Zend_Pdf(); 

		// Add new page to the document 
		
		//VIEJO $page = $this->createPage($pdf,'595:862:');
		$page = $this->createPage($pdf,'595:842:');
		$cliente = $this->modelCliente->queryID('TblCliente',$documento['idCliente']);
		$this->imprimirCabeceraNuevoFormato($documento,true,$cliente,$page);
		$page = $this->imprimirMovimientos($documento,$cliente,'idFactura',$page,$pdf);
		$this->imprimirResumenNuevoFormato($documento,$page);
		$this->imprimirReciboNuevoFormato($documento,true,$cliente,$page);
		$this->view->pdfData = $pdf->render(); 
		$this->view->numerofactura = $documento['numerofactura'];	
			
	}
		
		
		
//	function imprimirCabecera($documento,$cliente,$page)
//	{
//		$lh =15;
//		$thy = 740;  //nombre
//		$thx = 350;  // nombre, direccion, codigo postal
//			
//		
//		$direccion = $cliente['direccionfact'];
//		$cp = $cliente['codigopostalfact'];
//		$ciudad = $cliente['ciudadfact'];
//		if ($direccion == '' )
//		{
//			$direccion = $cliente['direccion'];
//			$cp = $cliente['codigopostal'];
//			$ciudad = $cliente['ciudad'];
//		}
//		$page->drawText($cliente['nombre'], $thx, $thy, 'UTF-8'); 			
//		$page->drawText($direccion , $thx, $thy-$lh, 'UTF-8');
//		$page->drawText( $cp. ' ' . $ciudad, $thx, $thy-$lh-$lh, 'UTF-8');
//		
//		$thx2 = 20;
//		// VIEJO $thy2 = 695;
//		$thy2 = 675;
//		$page->drawText($documento['numerofactura'], $thx2, $thy2, 'UTF-8');
//		$date = new Zend_Date($documento['fecha']);
//		$dateprint= $date->toString('dd-MM-YYYY');
//		$page->drawText($dateprint, $thx2 + 75, $thy2, 'UTF-8');
//					
//		if ($cliente['numrefproveedor']!='')
//			$page->drawText( $cliente['numrefproveedor'], 175, $thy2, 'UTF-8'); 
//		
//		$page->drawText($cliente['nif'], 245, $thy2, 'UTF-8');
//		
//	}
//	
		

		
//	function imprimirResumenFactura($documento,$page)
//	{
//		$sx = 20;
//		$sy = 315;
//		$page->drawText(sprintf("%6.02f",$documento['bruto']),$sx,$sy,'UTF-8');
//		$page->drawText(sprintf("%2.0f",$documento['descuentoaplicartotal']),110,$sy,'UTF-8');
//		$page->drawText(sprintf("%5.02f",$documento['descuento']),145,$sy,'UTF-8');
//		$page->drawText(sprintf("%6.02f",$documento['baseimponible']),220,$sy,'UTF-8');
//		$page->drawText(sprintf("%2.0f",$documento['tipoiva']),305,$sy,'UTF-8');
//		$page->drawText(sprintf("%5.02f",$documento['iva']),340,$sy,'UTF-8');
//		$page->drawText(sprintf("%6.02f",$documento['total']),515,$sy,'UTF-8');
//		
//	}
		
//	function imprimirRecibo($documento,$cliente,$page)
//	{
//		$sx = 130;
//		$sy = 262;
//		$page->drawText($documento['numerofactura'],$sx,$sy,'UTF-8');
//		$page->drawText('VALENCIA',250,$sy,'UTF-8');
//		$page->drawText(sprintf("%6.02f",$documento['total']),425,$sy,'UTF-8');
//		$sy2 = 240;
//		$date = new Zend_Date($documento['fecha']);
//		$dateprint= $date->toString('dd-MM-YYYY');
//		$page->drawText($dateprint,200,$sy2,'UTF-8');
//		$vencimiento = new Zend_Date($documento['vencimiento']);
//		$vencimientoprint= $vencimiento->toString('dd-MM-YYYY');
//		$page->drawText($vencimientoprint,400,$sy2,'UTF-8');
//		
//		//$page->drawText($documento['cantidadletra1'],175,203,'UTF-8');
//		//$page->drawText($documento['cantidadletra2'],105,203-12,'UTF-8');
//		
//		$page->drawText($cliente['banco'],200,162,'UTF-8');
//		//$page->drawText('....',433,162,'UTF-8'); //cccc
//		$page->drawText($cliente['sucursal'],160,150,'UTF-8');
//		$page->drawText($cliente['dc'],514,152,'UTF-8');
//		//$page->drawText('---',160,150-12,'UTF-8');  // otra linea de direccion 
//		$page->drawText($cliente['cuentabancaria'],411,150-12,'UTF-8');
//		
//		$page->drawText($cliente['nombre'],$sx,100,'UTF-8');
//		$direccion = $cliente['direccionfact'];
//		$cp = $cliente['codigopostalfact'];
//		$ciudad = $cliente['ciudadfact'];
//		if ($direccion == '' )
//		{
//			$direccion = $cliente['direccion'];
//			$cp = $cliente['codigopostal'];
//			$ciudad = $cliente['ciudad'];
//		}
//		$page->drawText($direccion,$sx,100-12,'UTF-8');
//		$page->drawText($cp .' - '. $ciudad,$sx,100-24,'UTF-8');
//		
//	}
	
	
 }
?>
