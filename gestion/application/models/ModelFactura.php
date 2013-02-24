<?php

require_once(APPLICATION_PATH . '/models/ModelBase.php');


class ModelFactura extends ModelBase {

	protected $_table;
	protected $_tableMovimiento;
	protected $_tableReferencia;
	protected $_tableAlbaran;
	protected $_tableSetting;
	protected $_TblVentaCaja;
	protected $_TblCliente;


	public function getTable($table) {
		/* initialize the table model */
			
		if ( ($table == 'TblFactura') || ($table == 'idFactura') )
		{
			if($this->_table === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblFactura.php');
				$this->_table = new DbTable_TblFactura();
			}
			return $this->_table;
		}
		else if ( ($table == 'TblMovimiento') || ($table == 'idMovimiento') )
		{
			if($this->_tableMovimiento === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblMovimiento.php');
				$this->_tableMovimiento = new DbTable_TblMovimiento();
			}
			return $this->_tableMovimiento;
		}
		else if ( ($table == 'TblReferencia') || ($table == 'idReferencia') )
		{
			if($this->_tableReferencia === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblReferencia.php');
				$this->_tableReferencia = new DbTable_TblReferencia();
			}
			return $this->_tableReferencia;
		}
		else if ( ($table == 'TblCliente') || ($table == 'idCliente') )
		{
			if($this->_TblCliente === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblCliente.php');
				$this->_TblCliente = new DbTable_TblCliente();
			}
			return $this->_TblCliente;
		}
		else if ( ($table == 'TblAlbaran') || ($table == 'idAlbaran') )
		{
			if($this->_tableAlbaran === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblAlbaran.php');
				$this->_tableAlbaran = new DbTable_TblAlbaran();
			}
			return $this->_tableAlbaran;
		}
		else if ( ($table == 'TblSetting')  )
		{
			if($this->_tableSetting === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblSetting.php');
				$this->_tableSetting = new DbTable_TblSetting();
			}
			return $this->_tableSetting;
		}
		else if ( ($table == 'TblVentaCaja')  )
		{
			if($this->_TblVentaCaja === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblVentaCaja.php');
				$this->_TblVentaCaja = new TblVentaCaja();
			}
			return $this->_TblVentaCaja;
		}

	}

	public function queryFactura($key,$value)
	{
		$tableId = 'TblFactura';
		$tablawhere = $tableId;
		if ($key == 'nombre' )
		{
			$tablawhere = 'clientes';
		}
		$where = $tablawhere.'.'. $key;
			
		if ($key == 'idCliente')
		{
			$where .= ' = \'' . $value . '\'';
		}
		else
		{
			$where .= ' like \'%' . $value . '%\'';
		}
			
		$query = $this->getTable($tableId)
		->select()
		->from(array($tableId => $tableId ),
		array('idFactura','numerofactura','fecha','estado','iva','total' ) )
		->join(array('clientes' => 'TblCliente'), 'clientes.idCliente = TblFactura.idCliente AND '. $where , array('nombre') )
		->order('TblFactura.fecha DESC')
		->setIntegrityCheck(false);
			
		return $this->getTable($tableId)->fetchAll($query);
	}

	public function getLatestRecibos($anterior,$posterior)
	{
		$tableId = 'TblFactura';
		$tablawhere = $tableId;

		$where = $tablawhere.'.vencimiento >= \''. $anterior . '\' AND ';
		$where.= $tablawhere.'.vencimiento <= \''. $posterior . '\' AND TblFactura.recibo=\'1\'';
			
		$query = $this->getTable($tableId)
		->select()
		->from(array($tableId => $tableId ),
		array('idFactura','numerofactura','fecha','total','condicionespago','vencimiento' ) )
		->join(array('clientes' => 'TblCliente'), 'clientes.idCliente = TblFactura.idCliente AND '. $where ,
		array('nombre','dc','banco','cuentabancaria','sucursal') )
		->order('TblFactura.vencimiento DESC')
		->setIntegrityCheck(false);
			
		return $this->getTable($tableId)->fetchAll($query);
	}

	public function getLatestAsientos($anterior,$posterior)
	{
		$tableId = 'TblFactura';
		$tablawhere = $tableId;

		$where = $tablawhere.'.fecha >= \''. $anterior . '\' AND ';
		$where.= $tablawhere.'.fecha <= \''. $posterior . '\' ';
			
		$query = $this->getTable($tableId)
		->select()
		->from(array($tableId => $tableId ),
		array('idFactura','numerofactura','fecha','total','condicionespago','vencimiento','iva','tipoiva','bruto' ) )
		->join(array('clientes' => 'TblCliente'), 'clientes.idCliente = TblFactura.idCliente AND '. $where ,
		array('nombre','dc','banco','cuentabancaria','sucursal','cuentaventa','cuentacompra') )
		->order('TblFactura.fecha DESC')
		->setIntegrityCheck(false);
			
		return $this->getTable($tableId)->fetchAll($query);
	}
	
	
	public function getLatestAsientosExportarCsv($anterior,$posterior)
	{
		$tableId = 'TblFactura';
		$tablawhere = $tableId;

		$where = $tablawhere.'.fecha >= \''. $anterior . '\' AND ';
		$where.= $tablawhere.'.fecha <= \''. $posterior . '\' ';
			
		$query = $this->getTable($tableId)
		->select()
		->from(array($tableId => $tableId ),
		array('numerofactura','fecha','ordencliente','condicionespago','vencimiento','baseimponible','tipoiva','iva','total' ) )
		->join(array('clientes' => 'TblCliente'), 'clientes.idCliente = TblFactura.idCliente AND '. $where ,
		array('cuenta'=> new Zend_Db_Expr( 'CONCAT(\'430\' , LPAD(cuentaventa,4,\'0\'))' ),'nombre','banco','cuentabancaria','dc','sucursal') )
		->order('TblFactura.fecha DESC')
		->setIntegrityCheck(false);
			
		return $this->getTable($tableId)->fetchAll($query);
	}
	
	public function queryUltimosFacturas()
	{
		$tableId = 'TblFactura';
		$query = $this->getTable($tableId)
		->select()
		->from(array($tableId => $tableId ),
		array('idFactura','numerofactura','fecha','estado','iva','total' ) )
		->join(array('clientes' => 'TblCliente'), 'clientes.idCliente = TblFactura.idCliente ', array('nombre') )
		->order('TblFactura.fecha DESC')
		->limit(20)
		->setIntegrityCheck(false);
		return $this->getTable($tableId)->fetchAll($query);
	}

	public function asignarfacturaalbaran($idFactura,$idAlbaran)
	{
		// Registrar albaran
		$albaran = $this->queryID('TblAlbaran',$idAlbaran);
		$albaran['idFactura'] = $idFactura;
		$albaran->save();
		//Registrar movimientos
		$res = $this->updateMovimientosConFactura($idFactura,$idAlbaran);

	}

	public function updateMovimientosConFactura($idFactura,$idAlbaran)
	{
		$data = array();
		$data['idFactura']=$idFactura;
		
			
		return $this->getTable('TblMovimiento')->updateData($data,$idAlbaran,'idAlbaran');
	}

	public function desasignarMovimientosConFactura($idAlbaran,$idFactura)
	{

		$where = 'idFactura = \'' . $idFactura . '\' AND idAlbaran = \'' .$idAlbaran.'\'';
			
		$movs = $this->getTable('TblMovimiento')->selectWhere($where);
			
		foreach ($movs as $mov )
		{
			$mov['idFactura']=null;
			$mov['descuento']='0';
			$mov->save();
		}
	}




	public function queryAlbaranesPendientes($idCliente=null)
	{
		$tableId = 'TblAlbaran';
			
		$where = '';
		if (isset($idCliente))
		{
			$where = ' AND TblAlbaran.idCliente =\''.$idCliente.'\'';
		}
			
		$query = $this->getTable($tableId)
		->select()
		->from(array($tableId => $tableId ),
		array('idAlbaran','numeroalbaran','fecha','entrada','estado','ordencliente' ) )
		->join(array('clientes' => 'TblCliente'),
						'clientes.idCliente = TblAlbaran.idCliente AND TblAlbaran.estado = \'1\' and (TblAlbaran.idFactura is null) and TblAlbaran.facturable = \'1\'' . $where , array('nombre') )
		->join(array('deps' => 'TblStockDeposito'), 'deps.idDeposito = TblAlbaran.idDeposito ', array('tienda') )
		->joinleft(array('facturas' => 'TblFactura'), 'facturas.idFactura = TblAlbaran.idFactura' , array('numerofactura'))
		->order('TblAlbaran.fecha DESC')
		->setIntegrityCheck(false);
			
		return $this->getTable($tableId)->fetchAll($query);
	}

	public function queryAlbaranesPorFactura($idFactura)
	{
		$tableId = 'TblAlbaran';
			

		$query = $this->getTable($tableId)
		->select()
		->from(array($tableId => $tableId ),
		array('idAlbaran','numeroalbaran','fecha','entrada','estado','ordencliente' ) )
		->join(array('clientes' => 'TblCliente'), 'clientes.idCliente = TblAlbaran.idCliente AND TblAlbaran.idFactura = \''.$idFactura .'\'' , array('nombre') )
		->join(array('deps' => 'TblStockDeposito'), 'deps.idDeposito = TblAlbaran.idDeposito ', array('tienda') )
		->joinleft(array('facturas' => 'TblFactura'), 'facturas.idFactura = TblAlbaran.idFactura' , array('numerofactura'))
		->order('TblAlbaran.fecha DESC')
		->setIntegrityCheck(false);
			
		return $this->getTable($tableId)->fetchAll($query);
	}

	public function queryFacturaPorAlbaran($idAlbaran)
	{
		$tableId = 'TblAlbaran';
			
		$query = $this->getTable($tableId)
		->select()
		->from(array($tableId => $tableId ),
		array('idAlbaran','numeroalbaran','fecha','entrada','estado' ) )
		->join(array('clientes' => 'TblCliente'), 'clientes.idCliente = TblAlbaran.idCliente AND TblAlbaran.idAlbaran = \''.$idAlbaran .'\'' , array('nombre') )
		->join(array('deps' => 'TblStockDeposito'), 'deps.idDeposito = TblAlbaran.idDeposito ', array('tienda') )
		->joinleft(array('facturas' => 'TblFactura'), 'facturas.idFactura = TblAlbaran.idFactura' , array('idFactura','numerofactura','fecha','estado','iva','total'))
		->order('TblAlbaran.fecha DESC')
		->setIntegrityCheck(false);
			
		return $this->getTable($tableId)->fetchAll($query);
	}

	public function create($tableId,array $data)
	{
		if ($tableId == 'idMovimiento')
		{
			return $this->createMovimiento($data);
		}
		else if ($tableId == 'idFactura' )
		{
			return $this->createFactura($data);
		}
	}
	public function generatefactura($datfactura)
	{
		$idCliente = $datfactura['idCliente'];
		$cliente = $this->queryID('TblCliente',$idCliente);

		$idFactura = $this->createfactura($datfactura,$cliente);
		 
		if ($idFactura>'0')
		{
			if (isset($datfactura['idAlbaran']))
			{
				$this->asignarfacturaalbaran($idFactura,$datfactura['idAlbaran']);
			}
			//echo 'Factura' . $idFactura;

			return $idFactura;
		}
		return 0;
	}

	public function createFactura(array $data,$cliente)
	{
		// Create number factura
		$sets = $this->getTable('TblSetting')->selectID('factura');
		$data['numerofactura'] = sprintf("F/%04d/%d",$sets['value'] , date('y'));
		$sets['value'] = intval($sets['value']) + 1;
		$sets->save();
			
		//echo 'Factura' . $data['numerofactura'] . 'Cliente' . $data['idCliente'];
		// Set date
		$data['fecha'] = date('Y-m-d');
			
		$data['condicionespago']=$cliente['condicionespago'];
			
		if ( (strcasecmp($cliente['condicionespago'],'contado')==0) ||
		(intval($cliente['vencimiento'])==0) )
		{
			$data['vencimiento']=date('Y-m-d');
		}
		else
		{

			$ven=time();
			$ven += intval($cliente['vencimiento']) * 24 * 60*60;
			//echo ' Vencimiento '. date_format($ven, 'Y-m-d');
			$day = (int) date('d',$ven);
			$seg = round($day/5);
			if ($seg==0)
			$day = 1;
			else
			$day = 5*$seg;

			$data['vencimiento'] = date("Y-m-d", mktime(0, 0, 0, date("m",$ven)  , $day, date("Y",$ven)));
			//echo 'Venc 2'.$data['vencimiento'];
		}
		 
		$factura = $this->getTable('TblFactura')->insertData($data);
			
		if (isset($factura))
		{
			//echo 'Numero Factura' . $factura;
			return $factura;
		}
		else
		{
			//echo 'NO CREADO';
				
			return null;
		}
	}

	public function createVentaCaja(array $data)
	{
		$venta = $this->getTable('TblVentaCaja')->insertData($data);
			
		if (isset($venta))
		{
			//echo 'Numero Factura' . $factura;
			return $venta;
		}
		else
		{
			//echo 'NO CREADO';
				
			return null;
		}
	}

		
	public function update($tableId,$data, $idValue)
	{
		if ($tableId == 'idMovimiento')
		{
			return $this->updateMovimiento($data,$idValue);
		}
		else if ($tableId == 'idFactura' )
		{
			return $this->updateFactura($data,$idValue);
		}
	}

	public function updateMovimiento($data, $idValue)
	{
		$data['idMovimiento'] = $idValue;

		return $this->getTable('TblMovimiento')->updateData($data,$idValue);

	}


	public function setalbaranfacturable($idAlbaran,$estado)
	{
		$albaran = $this->getTable('TblAlbaran')->selectID($idAlbaran);
		if (isset($albaran))
		{
			$albaran['facturable'] = $estado;
			$albaran->save();
		}
	}


	public function queryUltimasVentasCaja($idDeposito)
	{
		$tableId = 'TblVentaCaja';
			
		$query = $this->getTable($tableId)
		->select()
		->from(array($tableId => $tableId ),
		array('idAlbaran','idFactura','idDeposito','idTblVentaCaja' ) )
		->join(array('albaranes' => 'TblAlbaran'), 'TblVentaCaja.idAlbaran = albaranes.idAlbaran AND TblVentaCaja.idDeposito = \''.$idDeposito .'\'', array('idCliente','fecha','estado','entrada') )
		->join(array('clientes' => 'TblCliente'), 'clientes.idCliente = albaranes.idCliente' , array('nombre') )
		->joinleft(array('facturas' => 'TblFactura'), 'facturas.idFactura = albaranes.idFactura' , array('idFactura','numerofactura','total'))
		->order('albaranes.fecha DESC')
		->limit(20,0)
		->setIntegrityCheck(false);
			
		return $this->getTable($tableId)->fetchAll($query);
	}
}
?>