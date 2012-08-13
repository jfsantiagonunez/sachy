<?php

require_once(APPLICATION_PATH . '/models/ModelBase.php');


	class ModelAlbaran extends ModelBase {
		/* the dbtable model */
		protected $_table;
		protected $_tableMovimiento;
		protected $_tableReferencia;
		protected $_tableSetting;
		protected $_tabledeposito;

		public function getTable($table) {
			/* initialize the table model */
			
			if ( ($table == 'TblAlbaran') || ($table == 'idAlbaran') )
			{
				if($this->_table === null) {
					require_once(APPLICATION_PATH . '/models/DbTable/TblAlbaran.php');
					$this->_table = new DbTable_TblAlbaran();
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
			else if ( ($table == 'TblSetting')  )
			{
				if($this->_tableSetting === null) {
					require_once(APPLICATION_PATH . '/models/DbTable/TblSetting.php');
					$this->_tableSetting = new DbTable_TblSetting();
				}
				return $this->_tableSetting;
			}
			else if ( ($table == 'TblStockDeposito') ||  ($table == 'idDeposito') )
			{
				if($this->_tabledeposito === null) {
					require_once(APPLICATION_PATH . '/models/DbTable/TblStockDeposito.php');
					$this->_tabledeposito = new DbTable_TblStockDeposito();
				}

				return $this->_tabledeposito;
			}
		}
	
		public function queryAlbaran($key,$value)
		{
			$tableId = 'TblAlbaran';
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
							array('idAlbaran','numeroalbaran','fecha','entrada','estado' ) ) 
					->join(array('clientes' => 'TblCliente'), 'clientes.idCliente = TblAlbaran.idCliente AND '. $where , array('nombre') )
					->join(array('deps' => 'TblStockDeposito'), 'deps.idDeposito = TblAlbaran.idDeposito ', array('tienda') )
					->joinleft(array('facturas' => 'TblFactura'), 'facturas.idFactura = TblAlbaran.idFactura' , array('numerofactura'))
					->order('TblAlbaran.idAlbaran DESC')
					->setIntegrityCheck(false);
					
			return $this->getTable($tableId)->fetchAll($query);	
		}
		
		
		public function queryUltimosAlbaranes()
		{
			$tableId = 'TblAlbaran';
			$query = $this->getTable($tableId)
					->select()
					->from(array($tableId => $tableId ), 
							array('idAlbaran','numeroalbaran','fecha','entrada','estado' ) ) 
					->join(array('clientes' => 'TblCliente'), 'clientes.idCliente = TblAlbaran.idCliente ', array('nombre') )
					->join(array('deps' => 'TblStockDeposito'), 'deps.idDeposito = TblAlbaran.idDeposito ', array('tienda') )
					->joinleft(array('facturas' => 'TblFactura'), 'facturas.idFactura = TblAlbaran.idFactura' , array('numerofactura'))
					->order('TblAlbaran.idAlbaran DESC')
					->limit(20)
					->setIntegrityCheck(false);
			return $this->getTable($tableId)->fetchAll($query);	
		}
		
		
		public function querySumMovimientos($foregnkey,$value)
		{
			$tableId = 'TblMovimiento';
			$query = $this->getTable($tableId)
					->select()
					->from(array($tableId => $tableId ), 
							array('consumo' => new Zend_Db_Expr('SUM(cantidad)' ) ) ) 
					->join(array('albaran' => 'TblAlbaran'), 'albaran.idAlbaran=TblMovimiento.idAlbaran AND albaran.' . $foregnkey . ' = \'' . $value . '\'', array('idAlbaran') )
					->join(array('refs' => 'TblReferencia'), 'refs.idReferencia = TblMovimiento.idReferencia ', array('calidad','color','tipoenvase','idReferencia' ) )
					->group('TblMovimiento.idReferencia')
					->setIntegrityCheck(false);
			return $this->getTable($tableId)->fetchAll($query);		
		}
		
		public function queryReferenciasClientes($value)
		{
			$tableId = 'TblMovimiento';
			$fecha = date('Y');
			
			$query = $this->getTable($tableId)
					->select()
					->from(array($tableId => $tableId ), 
							array('consumo' => new Zend_Db_Expr('SUM(cantidad)' ) ) ) 
					->join(array('albaran' => 'TblAlbaran'), 'albaran.idAlbaran=TblMovimiento.idAlbaran AND YEAR(albaran.fecha) = \''. $fecha. '\' AND TblMovimiento.idReferencia = \'' . $value . '\'', array('idAlbaran') )
					->join(array('clts' => 'TblCliente'), 'clts.idCliente = albaran.idCliente ', array('idCliente','nombre','nif' ) )
					->group('clts.idCliente')
					->setIntegrityCheck(false);
			return $this->getTable($tableId)->fetchAll($query);		
		}


		
		public function queryAlbaranPorReferencias($value)
		{
			$tableId = 'TblAlbaran';
			$fecha = date('Y'); // AND YEAR(albaran.fecha) = \''. $fecha. '\''
			
			$query = $this->getTable($tableId)
					->select()
					->from(array('albaran' => $tableId ), 
							array('idAlbaran','fecha','estado','entrada','numeroalbaran' ) ) 
					->join(array('mov' => 'TblMovimiento'), 'albaran.idAlbaran=mov.idAlbaran AND  mov.idReferencia = \'' . $value . '\'', array('idReferencia') )
					->join(array('clts' => 'TblCliente'), 'clts.idCliente = albaran.idCliente ', array('nombre' ) )
					->join(array('deps' => 'TblStockDeposito'), 'deps.idDeposito = albaran.idDeposito ', array('tienda') )
					->joinleft(array('facturas' => 'TblFactura'), 'facturas.idFactura = albaran.idFactura' , array('numerofactura'))
					->order('albaran.fecha DESC')
					->group('albaran.idAlbaran')
					->setIntegrityCheck(false);

			return $this->getTable($tableId)->fetchAll($query);		
		}
		
		
		public function create($tableId,array $data)
		{
			if ($tableId == 'idMovimiento')
			{
				return $this->createMovimiento($data);
			}
			else if ($tableId == 'idAlbaran' )
			{
				return $this->createAlbaran($data);
			}
		}
		
		public function createAlbaran(array $data)
		{
			$data['fecha'] = date('Y-m-d');
			$sets = $this->getTable('TblSetting')->selectID('albaran');
			$data['numeroalbaran'] = $sets['value'] . '/' . date('Y');
			$sets['value'] = intval($sets['value']) + 1;
			$sets->save();

			//print_r($data);
			$albaran = $this->getTable('TblAlbaran')->insertData($data);

			if (isset($albaran))
			{
				//echo 'Numero Albaran' . $albaran['idAlbaran'];
				return $albaran['idAlbaran'];
				
			}
			else
			{
				echo 'NO CREADO';
			
				return ;
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
			else if ($tableId == 'idAlbaran' )
			{
				return $this->updateAlbaran($data,$idValue);
			}
		}
		
		public function updateMovimiento($data, $idValue)
		{
			$data['idMovimiento'] = $idValue;
		
			return $this->getTable('TblMovimiento')->updateData($data,$idValue);
		}
		
		public function registrarStock($idAlbaran)
		{
			$albaran = $this->getTable('TblAlbaran')->selectID($idAlbaran);
			
			$depottransfer = '0';
			if ($albaran['tipotransfer']=='1')
			{
				$depot = $this->getTable('TblStockDeposito')->selectFK($albaran['idCliente'])->toArray();
				$depottransfer = 'loc' . $depot[0]['idDeposito'];
			}
			
			$deposito = 'loc' . $albaran['idDeposito'];
			$movimientos = $this->queryMovimientos('idAlbaran',$idAlbaran);
			
			foreach ($movimientos as $mov)
			{	

				$actual = $this->getTable('TblReferencia')->selectID($mov['idReferencia']);

				$actual[ $deposito ] = $actual[$deposito] - $mov['tcantidad'];
				
				if ($depottransfer != '0' )
				{
					$actual[ $depottransfer ] = $actual[$depottransfer] + $mov['tcantidad'];
				}
				
				$actual->save();
			}
			
			// Actualizar el albaran como terminado
			$albaran['estado'] = '1';
			$albaran->save();
			
			return;	
		}

	}
?>