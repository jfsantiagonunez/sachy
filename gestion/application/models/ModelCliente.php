<?php

require_once(APPLICATION_PATH . '/models/ModelBase.php');


	class ModelCliente extends ModelBase {
		/* the dbtable model */
		protected $_table;
		protected $_tableId = 'TblCliente';
		protected $_tabladescuento;

		public function getTable($tablename) {
			/* initialize the table model */
		if ( ($tablename == 'TblCliente') ||  ($tablename == 'idCliente') )
		{
			if($this->_table === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblCliente.php');
				$this->_table = new DbTable_TblCliente();
			}
			return $this->_table;
		}
		else if ( ($tablename == 'TblDescuento') ||  ($tablename == 'idDescuento') )
		{
			if($this->_tabladescuento === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblDescuento.php');
				$this->_tabladescuento = new DbTable_TblDescuento();
			}

			return $this->_tabladescuento;
		}
	
		}
		
		public function getDefaultDescuento($idCliente)
		{
			$cliente = $this->getTable('TblCliente')->selectId($idCliente);
			$descuentoId = $cliente['idDescuento'];
			if (isset($descuentoId) && ( $descuentoId != '0' ) )
			{
				$descuento = $this->getTable('TblDescuento')->selectId($descuentoId);
				return $descuento['descuento'];
			}
			else
			{
				$desc=$this->getTable('TblDescuento')->selectFK($idCliente);
				
				if ($desc->count() > 0 )
				{
					$cliente['idDescuento'] = $desc[0]['idDescuento'];
					$cliente->save();
					return $desc[0]['idDescuento'];
				}
				
			}
			return '0';
		}
		
		public function queryDescuentosByType($idCliente,$type)
		{
			$results = array();
			$results['0'] = '0';
			$select = $this->getTable('TblDescuento')->select()
				->from('TblDescuento',array('descuento'))
				->where( 'idCliente = \''.$idCliente.'\' AND tipoDescuento=\''.$type.'\'' )
				->order('descuento asc')
				->group('descuento');
				
			$resultquery= $this->getTable('TblDescuento')->fetchAll($select);
			foreach ($resultquery as $result)
			{
				$valor = $result['descuento'];
				$results[$valor]=$valor;
			}
			
			$results['100'] = '100';
			return $results;
		}
		
		public function queryAutocompletionDescuento($idCliente,$calidad)
		{
			$results = array();

			if ( (!empty($calidad)) && (!empty($idCliente)) )
			{

				// QUERY DESCUENTO POR CALIDAD
				$select = $this->getTable('TblDescuento')->select()
				->from('TblDescuento',array('descuento'))
				->where( 'idCliente = \''.$idCliente.'\' and calidad = \''.$calidad.'\' AND tipoDescuento=\'1\'' )
				->order('descuento asc')
				->group('descuento');
				
				$resultquery= $this->getTable('TblDescuento')->fetchAll($select);
				
				if ($resultquery->count() == 0  )
				{
					// QUERY DESCUENTO OTRAS
					$select = $this->getTable('TblDescuento')->select()
					->from('TblDescuento',array('descuento'))
					->where( 'idCliente = \''.$idCliente.'\' and tipoDescuento=\'2\'' )
					->order('descuento asc')
					->group('descuento');
					$resultquery = $this->getTable('TblDescuento')->fetchAll($select);
				}
				
				$results=$resultquery->toArray();
			}
			$count=count($results);
			// ANADIR descuento 0 y 100
			$results[$count] = array('descuento'=>'0');
			$results[$count+1] = array('descuento'=>'100');
			return $results;
		}
	}
?>