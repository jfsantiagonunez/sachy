<?php

require_once(APPLICATION_PATH . '/models/ModelBase.php');


	class ModelFactura extends ModelBase {

		protected $_table;
		protected $_tableMovimiento;
		protected $_tableReferencia;
		protected $_tableAlbaran;
		protected $_tableSetting;


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
		
		
		
		public function updateMovimientosConFactura($idFactura,$idAlbaran,$descuento)
		{
			$data = array();
			$data['idFactura']=$idFactura;
			$data['descuento']=$descuento;
			
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
							array('idAlbaran','numeroalbaran','fecha','entrada','estado' ) ) 
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
							array('idAlbaran','numeroalbaran','fecha','entrada','estado' ) ) 
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
		
		public function createFactura(array $data,$cliente)
		{
			// Create number factura
			$sets = $this->getTable('TblSetting')->selectID('factura');
			$data['numerofactura'] = $sets['value'] . '/' . date('Y');
			$sets['value'] = intval($sets['value']) + 1;
			$sets->save();
			
			//echo 'Factura' . $data['numerofactura'] . 'Cliente' . $data['idCliente'];
			// Set date 
			$data['fecha'] = date('Y-m-d');
			
			$data['condicionespago']=$cliente['condicionespago'];
			
			if ( (strcmp($cliente['condicionespago'],'contado')==0) || 
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
		
		public function createMovimiento(array $data)
		{
			
			$ref = $this->getTable('TblReferencia')->selectPKs($data );
			
			if (isset($ref))
			{
				$data['idReferencia'] = $ref['idReferencia'];
			
				$idMov = $this->getTable('TblMovimiento')->insertData($data);
				if (isset($idMov))
				{
					return $idMov;
				}
				else
				{
					echo 'NO MOVIMIENTO';
			
					return null ;
				}
			}
			else
			{
				echo 'NO REFERENCIA';
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
		
		public function contabilizarFactura($idFactura)
		{
			$factura = $this->getTable('TblFactura')->selectID($idFactura);
			$sett = $this->getTable('TblSetting')->selectID('iva');
			$tipoiva = floatval($sett['value']);
			$movimientos = $this->queryMovimientos('idFactura',$idFactura);
			$total = 0;
			$bruto = 0;
			$descacc = 0;
			
			foreach ($movimientos as $entry)
			{
				$brutomov = floatval($entry['tcantidad']) * floatval($entry['precio'] );
				
				if (floatval($entry['tipoenvase']) > 1 )
				{
					$brutomov *= floatval($entry['tipoenvase']);
				}
				
				$descuento = round( $brutomov * floatval($entry['descuento'])/100 , 2);
				$totalmov = $brutomov - $descuento;
				
				$descacc += $descuento;
				$bruto += $brutomov;
				
				$total += $totalmov;
			}
			
			// Actualizar el albaran como terminado
			$factura['estado'] = '1';
			$factura['tipoiva'] = $tipoiva;
			
			$iva = round( $total * $tipoiva/100,2);
			
			$factura['bruto']=$bruto;
			$factura['descuento']=$descacc;
			$factura['descporc']=round($descacc*100/$bruto,0);
			$factura['baseimponible']=$total;
			$factura['iva'] = $iva;
			$factura['total']=$total+$iva;
			$factura->save();
			
			return;	
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
		
	
		
	}
?>