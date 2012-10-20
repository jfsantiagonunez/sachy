<?php

require_once(APPLICATION_PATH . '/controllers/BaseController.php');

class CajaController extends BaseController
{
	var $model;
	var $modelAlbaran;
	var $modelStockLocation;
	var $modelCatalogo;
	var $modelCliente;
	var $titleView;
	var $defaultDeposito;
	protected $_idkey = 'idTblVentaCaja';
	protected $_controllername = 'caja';
	protected $_flashMessenger = null;

	public function init()
	{

		$this->titleView='Gestion Caja';
		require_once(APPLICATION_PATH . '/models/ModelFactura.php');
		$this->model = new ModelFactura();
		require_once(APPLICATION_PATH . '/models/ModelCatalogo.php');
		$this->modelCatalogo = new ModelCatalogo();
		require_once(APPLICATION_PATH . '/models/ModelAlbaran.php');
		$this->modelAlbaran = new ModelAlbaran();
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

		$this->view->idDeposito = $this->getRequest()->getParam('idDeposito');

		if (!isset($this->view->idDeposito))
		{
			$this->view->idDeposito = $this->defaultDeposito;
		}
		$depot = $this->modelCatalogo->queryID('TblStockDeposito',$this->view->idDeposito);
		$this->view->titleView = 'Gestion Caja ['.$depot['tienda'].']';
		if($this->getRequest()->isPost())
		{
			$data = $this->getRequest()->getPost();

			// Tipos de Post : Cambio de Caja
			$cajaseleccionada = $this->getRequest()->getParam('cajaseleccionada');
			if (isset($cajaseleccionada))
			{
				//if (isset($data['idDeposito']))
				//{
				// Call back to the same, but push the idDeposito in the params.
				//	return $this->_helper->redirector->gotoRoute(array(
				//		'controller' => 'caja' , 'action' => 'index', 'idDeposito' => $data['idDeposito'] ),
				//		'default', true);
				//}
			}

			$this->view->entrada = '0';  // Siempre salida por defecto
			// Generar nueva venta cliente
			$nuevaventacliente = $this->getRequest()->getParam('nuevaventacaja');
			if (isset($nuevaventacliente))
			{

				// Crear venta usando idTienda
				if (isset($depot))
				{
					$datalbaran = array('idCliente'=>$depot['idCliente'],
	    									'entrada'=>$this->view->entrada,
	    									'idDeposito'=>$this->view->idDeposito);
					return $this->generateVentaCaja($datalbaran);
				}
				else
				{
					$this->view->subtitleView =  'Tienda no esta asociado con un cliente';

				}
			}
			$clientenuevaventacaja = $this->getRequest()->getParam('clientenuevaventacaja');
			if (isset($clientenuevaventacaja))
			{


				// Determinar siguiente paso en funcion del numero de clientes encontrados.
				if (isset($data['cliente']))
				{


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
						$datalbaran = array('idCliente'=>$idCliente,
	    									'entrada'=>$this->view->entrada,
	    									'idDeposito'=>$this->view->idDeposito);
						return $this->generateVentaCaja($datalbaran);

					}
				}
			}

		}

		$this->view->messages = $this->_flashMessenger->getMessages();
		$this->view->imprimirId = $this->getRequest()->getParam('imprimirId');

		$depositos =  $this->modelCatalogo->queryAll('idDeposito')->toArray();

		$this->view->selectTienda = $this->generateSelect( 'idDeposito','idDeposito' , $depositos , $this->view->idDeposito, 'tienda');

		$this->view->tituloBusqueda = 'Ultimas Operaciones Caja';
		//$caja = $this->modelCatalogo->queryID('TblStockDeposito',$this->view->idDeposito);
		$this->view->operacionescaja = $this->model->queryUltimasVentasCaja($this->view->idDeposito);

	}

	public function nuevaventacajaAction()
	{
		$this->view->titleView='Venta Caja';
		$this->view->subtitleView = 'Introducir Referencias';

		$done = $this->getRequest()->getParam('Agregar');
		if (isset($done) && ($done=='Agregar'))
		{
			//This is handle somewhere else. See stock/nuevomovimiento.phtml
			return;
		}

		// Get movimientos
		$idTblVentaCaja = $this->getRequest()->getParam('idTblVentaCaja');
		$idAlbaran=null;
		if (isset($idTblVentaCaja))
		{
			$venta = $this->model->queryID('TblVentaCaja',$idTblVentaCaja);
			$idAlbaran = $venta['idAlbaran'];
		}
		else
		{
			$idAlbaran = $this->getRequest()->getParam('idAlbaran');
		}

		if (!isset($idAlbaran))
		{
			echo 'Algo fallo en el hiperspacio';
			return;
		}

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
		$this->view->paramspartial['controller'] = 'caja';
		$this->view->paramspartial['entrada'] = $albaran['entrada'];
		$this->view->paramspartial['idCliente'] = $albaran['idCliente'];

		$this->view->listadodiv= $this->listmovimientosreturn($albaran->toArray());
		$this->view->descuentos = $this->modelCliente->queryFK('TblDescuento',$albaran['idCliente']);
	}

	public function editAction()
	{
		$idTblVentaCaja = $this->getRequest()->getParam('idTblVentaCaja');

		return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'caja' , 'action' => 'nuevaventacaja', 'idTblVentaCaja' => $idTblVentaCaja ),
					'default', true);
	}

	public function elegirclienteAction()
	{
		$idCliente = $this->getRequest()->getParam('idCliente');
		$entrada = $this->getRequest()->getParam('entrada');
		$idDeposito = $this->getRequest()->getParam('idDeposito');
		$datalbaran = array('idCliente'=>$idCliente,'entrada'=>$entrada,'idDeposito'=>$idDeposito);
		return $this->generateVentaCaja($datalbaran);
	}

	public function generateVentaCaja($datalbaran)
	{

		$datalbaran['descuentoaplicartotal']='0';

		// Generar albaran
		$albaran = $this->modelAlbaran->createAlbaran($datalbaran);

		// Generar venta caja
		$ventacajadata = array('idDeposito'=>$datalbaran['idDeposito'],'idAlbaran'=>$albaran);

		$ventacaja = $this->model->createVentaCaja($ventacajadata);

		if (isset($albaran)&&isset($ventacaja))
		{
			return $this->_helper->redirector->gotoRoute(array(
					'controller' => 'caja' , 'action' => 'nuevaventacaja', 'idAlbaran' => $albaran ),
					'default', true);
		}
	}

	public function deleteAction()
	{

		$idValue = $this->getRequest()->getParam('idTblVentaCaja');

		if ($idValue)
		{
			$ventacaja = $this->model->queryID('TblVentaCaja',$idValue);
			$this->model->delete('TblAlbaran',$ventacaja['idAlbaran']);
			$this->model->delete('TblVentaCaja',$idValue);

		}
		$retorno = array();
		$retorno['controller'] = 'caja';
		$retorno['action']='index';
		$idDeposito = $this->getRequest()->getParam('idDeposito');

		if (isset($idDeposito))
		{
			$retorno['idDeposito']=$idDeposito;
		}
		return $this->_helper->redirector->gotoRoute($retorno,
						'default', true);

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
				$this->salirAccion('caja');
				break;
			case 'Solo Guardar':
				return $this->gestionarventacaja(true,false);
				break;
			case 'Volver a Imprimir':
			case 'Facturar':
				return $this->gestionarventacaja(false,true);
				break;
			case 'Imprimir & Guardar':
				return $this->gestionarventacaja(true,true);
				break;
		}
	}


	public function gestionarventacaja($contabilizarstock,$imprimir)
	{
		$idAlbaran = $this->getRequest()->getParam('idAlbaran');
		if (!isset($idAlbaran))
		{
			echo 'SYSTEM ERROR';
			return;
		}
		$ventacajas = $this->model->queryFK('TblVentaCaja',$idAlbaran);

		$idFactura = $ventacajas[0]['idFactura'];
		$albaran = $this->model->queryID('TblAlbaran',$idAlbaran);

		// Chequear estado
		$estado = $albaran['estado'];

		// Si el estado no es terminado, ignoramos la accion y lo registramos en el stock, ANYWAY

		if ($estado == '0')
		{
			// Nota. Aqui el estado de factura no se tiene en cuenta. No hay estado intermedio de factura.
			// Una vez que se aprueba el albaran, se crea la factura y se contabiliza
			$albaran['descuentoaplicartotal']=$this->getRequest()->getParam('descuento');
			$this->modelAlbaran->registrarStock($idAlbaran);

			// Crear Factura
			$datfactura = array('idAlbaran'=>$idAlbaran,
						'idCliente'=>$albaran['idCliente'],
						'descuentoaplicartotal'=>$albaran['descuentoaplicartotal']);
			$idFactura = $this->model->generatefactura($datfactura); //Esto ya asigna la factura al albaran y los movimientos correspondientes

			if ($idFactura==0)
			{
				// !! OSTI TU... k FOLLON
				// MIREMOS PARA OTRO LADO... y dejemos que se rompa
			}

			$ventacajas[0]['idFactura'] = $idFactura;
			$ventacajas[0]->save();

			$factura = $this->model->queryID('TblFactura',$idFactura);
			// Contabilizar Factura
			$this->model->contabilizarObjeto('idFactura',$factura,$idFactura);

		}

		$this->salirAccion('caja',$imprimir,$idFactura);
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
}
?>
