<?php

	class ModelBase {
		/* the dbtable model */
		protected $_table;

		//Define the getTable in the TblBase derived class
			public function getTableId()
		{
			return $this->_tableId;
		}
		
		public function queryID($tableId,$key)
		{
			return $this->getTable($tableId)->selectID($key);
		}
		
		public function queryPK($tableId,$key) {
			return $this->getTable($tableId)->selectPK($key);
		}
		
		public function queryFK($tableId,$key) {
			return $this->getTable($tableId)->selectFK($key);
		}
	
		public function queryAll($tableId) {
			$select = $this->getTable($tableId)->select();		
			return $this->getTable($tableId)->fetchAll($select);
		}
		
		public function countAll($tableId,$where) {
			$select = $this->getTable($tableId)->select()->from($this->getTable($tableId), array('count(*) as amount'))->where($where );
        	$rows = $this->getTable($tableId)->fetchAll($select);
        	return($rows[0]->amount);
		}
		
		
		public function query($tableId,$keyword)
		{
			return $this->getTable($tableId)->selectWhere( $this->getTable($tableId)->getPk() . ' LIKE \'%' . $keyword . '%\' OR nif LIKE \'%' . $keyword . '%\'' );
		}
		
		public function queryWhere($tableId,$where)
		{
			return $this->getTable($tableId)->selectWhere( $this->getTable($tableId)->getPk() . $where );
		}
		
		public function create($tableId,array $data)
		{
			return $this->getTable($tableId)->insertData($data);
		}
		
		public function update($tableId,array $data, $id) {
			return $this->getTable($tableId)->updateData($data,$id);
		}
		
		public function updateDataWhereLike($tableId,array $data, $fieldwhere, $idmatch) {
			return $this->getTable($tableId)->updateDataWhereLike($data,$fieldwhere, $idmatch);
		}
		
		public function delete($tableId,$id){
			
			/* create where clause */
			$ttable = $this->getTable($tableId);
			$where = $ttable->getAdapter()->quoteInto($ttable->getId() . ' = ?', $id);

			
			return $ttable->delete($where);
		}
		
		public function  queryToDisplay($tabla,$id=null,$where=null)
		{
			if (isset($where))
			{
				$data = $this->getTable($tabla)->selectWhere($where);
			}
			else if (isset($id))
				$data = $this->queryFK($tabla,$id);
			else
				$data = $this->queryAll($tabla);
			$ret = array();
			$pk = $this->getTable($tabla)->getPK();
			$id = $this->getTable($tabla)->getId();
			Foreach($data as $res )
			{
				$ret[$res[$id]] = $res[$pk];
			}
			return $ret;
		
		}
		
		public function  queryToDisplayValues($tabla,$id=null)
		{
			if (isset($id))
				$data = $this->queryFK($tabla,$id);
			else
				$data = $this->queryAll($tabla);
			$ret = array();
			$pk = $this->getTable($tabla)->getPK();
			$id = $this->getTable($tabla)->getId();
			Foreach($data as $res )
			{
				$ret[$res[$pk]] = $res[$pk];
			}
			return $ret;
		
		}
		
		public function queryMovimientos($foregnkey,$value)
		{
			$tableId = 'TblReferencia';
			$query = $this->getTable($tableId)
					->select()
					->from(array($tableId => $tableId ), 
							array('idReferencia','calidad','color','tipoenvase','precio','(precio*tipoenvase) as tprecio') ) 
					->join(array('movs' => 'TblMovimiento'), 
							'movs.idReferencia = TblReferencia.idReferencia AND movs.'. $foregnkey. '= \''.$value.'\'', 
							array('idMovimiento','(cantidad*salida) as tcantidad' ,'idAlbaran','descuento','salida') )
					->join(array('cals' => 'TblCalidad'), 'cals.calidad = TblReferencia.calidad ', array('descripcion' ) )
					->order('movs.idAlbaran')
					->setIntegrityCheck(false);
			$movimientos = $this->getTable($tableId)->fetchAll($query);	
			foreach ($movimientos as $movimiento)
			{
				$preciocondescuento = round(floatval($movimiento['precio'] ) * (100-floatval($movimiento['descuento']))/100,3);
				$cantidadunidades=floatval($movimiento['tcantidad']);
				if (floatval($movimiento['tipoenvase']) > 1 )
				{
					 $cantidadunidades*= floatval($movimiento['tipoenvase']);
				}
				$movimiento['tprecio'] = round( $cantidadunidades * $preciocondescuento,2) ;
//				if (floatval($movimiento['tipoenvase']) > 1 )
//				{
//					$movimiento['tprecio'] *= floatval($movimiento['tipoenvase']);
//				}
			}
			
			return $movimientos;
		}
		
		public function getTipoPago()
		{
			$listtipopago = array();
			$listtipopago['cheque'] = 'Cheque';
			$listtipopago['pagare'] = 'Pagare';
			$listtipopago['giro'] = 'Giro';
			$listtipopago['transferencia'] = 'Transferencia';
			$listtipopago['confirming'] = 'Confirming';
			$listtipopago['contado'] = 'Contado';
			$listtipopago['cheque'] = 'Cheque';
			$listtipopago['tarjetacredito'] = 'Tarjeta Credito';
			return $listtipopago;
		}
		
		public function getDiasPago()
		{
			$vencimientos = array();
			$vencimientos[0] = '0 dias';
			$vencimientos[30] = '30 dias';
			$vencimientos[45] = '45 dias';
			$vencimientos[60] = '60 dias';
			$vencimientos[70] = '70 dias';
			$vencimientos[75] = '75 dias';
			$vencimientos[85] = '85 dias';
			$vencimientos[90] = '90 dias';
			$vencimientos[120] = '120 dias';
			return $vencimientos;
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
		
		
		public function contabilizarObjeto($key,$objeto,$idObjeto)
		{
	
			$sett = $this->getTable('TblSetting')->selectID('iva');
			$tipoiva = floatval($sett['value']);
			$movimientos = $this->queryMovimientos($key,$idObjeto);
			
			$bruto = 0;
			
			
			foreach ($movimientos as $entry)
			{
				$brutomov = floatval($entry['tcantidad']) * floatval($entry['precio'] );
				
				if (floatval($entry['tipoenvase']) > 1 )
				{
					$brutomov *= floatval($entry['tipoenvase']);
				}
				
				$descuentomov = round( $brutomov * floatval($entry['descuento'])/100 , 2);
				$totalmov = $brutomov - $descuentomov;
				
				$bruto += $totalmov;
			}
			
			// Actualizar el objeto como terminado
			$objeto['estado'] = '1';
						
			$objeto['bruto']=$bruto;
			$descuentoaplicar=floatval($objeto['descuentoaplicartotal']);
			$descuento=round($descuentoaplicar*$bruto/100,2);
			$objeto['descuento']=$descuento;
			$baseimp=$bruto-$descuento;			
			$objeto['baseimponible']=$baseimp;
			$iva = round( $baseimp * $tipoiva/100,2);
			$objeto['iva'] = $iva;
			$objeto['tipoiva'] = $tipoiva;
			$objeto['total']=$baseimp+$iva;
			$objeto->save();
			
			return;	
		}
		
	}
?>