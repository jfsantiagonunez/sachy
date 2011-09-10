<?php

require_once(APPLICATION_PATH . '/models/ModelBase.php');

	class ModelCatalogo extends ModelBase {
		/* the dbtable model */
		protected $_tblcalidad;     // TblCalidad
		protected $_tblcategoria;   // TblCategoria
		protected $_tblreferencia;  // TblReferencia
		protected $_tblmarca;       // TblMarca
		protected $_tblmovimiento;     // TblMovimiento
		protected $_tbldeposito;     // TblMovimiento

		public function getTable($table) {
			/* initialize the table model */
			if ( ($table=='TblCalidad') || ($table == 'calidad') )
			{
				if ($this->_tblcalidad === null) {
					require_once(APPLICATION_PATH . '/models/DbTable/TblCalidad.php');
					$this->_tblcalidad = new DbTable_TblCalidad();
				}
				return $this->_tblcalidad;
			}
			else if ( ($table=='TblReferencia') || ($table == 'idReferencia') )
			{
				if ($this->_tblreferencia === null) {
					require_once(APPLICATION_PATH . '/models/DbTable/TblReferencia.php');
					$this->_tblreferencia = new DbTable_TblReferencia();
				}
				return $this->_tblreferencia;
			}
			else if (($table=='TblCategoria') || ($table == 'idCategoria') )
			{
				if ($this->_tblcategoria === null) {
					require_once(APPLICATION_PATH . '/models/DbTable/TblCategoria.php');
					$this->_tblcategoria = new DbTable_TblCategoria();
				}
				return $this->_tblcategoria;
			}
			else if (($table=='TblMarca') || ($table == 'idMarca') )
			{
				if ($this->_tblmarca === null) {
					require_once(APPLICATION_PATH . '/models/DbTable/TblMarca.php');
					$this->_tblmarca = new DbTable_TblMarca();
				}
				return $this->_tblmarca;
			}
			else if (($table=='TblMovimiento') || ($table == 'idMovimiento') )
			{
				if ($this->_tblmovimiento === null) {
					require_once(APPLICATION_PATH . '/models/DbTable/TblMovimiento.php');
					$this->_tblmovimiento = new DbTable_TblMovimiento();
				}
				return $this->_tblmovimiento;
			}
			else if ( ($table == 'TblStockDeposito') ||  ($table == 'idDeposito') )
			{
				if($this->_tbldeposito === null) {
					require_once(APPLICATION_PATH . '/models/DbTable/TblStockDeposito.php');
					$this->_tbldeposito = new DbTable_TblStockDeposito();
				}

				return $this->_tbldeposito;
			}
		}
	
		
		public function queryCalidades($keyword)
		{
			$query = $this->getTable('TblCalidad')
					->select()
					->from(array('TblCalidad' => 'TblCalidad' ), 
							array('calidad','descripcion' ) ) 
					->join(array('marca' => 'TblMarca'), 'marca.idMarca = TblCalidad.idMarca AND TblCalidad.calidad like \'%' . $keyword . '%\'', array('marca') )
					->join(array('categoria' => 'TblCategoriaProducto'), 'categoria.idCategoria = TblCalidad.idCategoria' , array('categoria'))
					->group('TblCalidad.calidad')
					->setIntegrityCheck(false);
			return $this->getTable('TblCalidad')->fetchAll($query);		
		}
		
		public function queryAutocompletionCalidades($keyword)
		{
			$select = $this->getTable('TblCalidad')->select()
			->from('TblCalidad',array('calidad'))
			->where( 'calidad like \''.$keyword.'%\'' )
			->order('calidad asc');
			return $this->getTable('TblCalidad')->fetchAll($select);
			
		}
		
		public function queryAutocompletionColor($keyword,$calidad)
		{
			$select = $this->getTable('TblReferencia')->select()
			->from('TblReferencia',array('color'))
			->where( 'color like \''.$keyword.'%\' and calidad = \''.$calidad.'\'' )
			->order('color asc')
			->group('color');
			return $this->getTable('TblReferencia')->fetchAll($select);
			
		}
		
		public function queryAutocompletionTipoEnvase($keyword,$calidad,$color)
		{
			$select = $this->getTable('TblReferencia')->select()
			->from('TblReferencia',array('tipoenvase'))
			->where( 'tipoenvase like \''.$keyword.'%\' and calidad = \''.$calidad.'\' and color = \''.$color.'\'' )
			->order('tipoenvase asc')
			->group('tipoenvase');
			return $this->getTable('TblReferencia')->fetchAll($select);
			
		}
		
		public function queryReferencias($keyword)
		{
			$query = $this->getTable('TblReferencia')
					->select()
					->where('calidad like \'%' . $keyword . '%\'')
					->order(array('calidad', 'color' , 'tipoenvase'));
			return $this->getTable('TblReferencia')->fetchAll($query);	

		}
			
		public function queryStockUltimosMovimientos()
		{
				$query = $this->getTable('TblMovimiento')
					->select()
					->from(array('mov' => 'TblMovimiento' ), 
							array('idReferencia','idAlbaran' )  )
					->join(array('ref' => 'TblReferencia'), 'ref.idReferencia = mov.idReferencia ', array('calidad','color','tipoenvase','loc1','loc2','loc3','loc4','loc5') )
					->group('mov.idReferencia')
					->order('mov.idAlbaran DESC')
					->limit(20)
					->setIntegrityCheck(false);
			return $this->getTable('TblMovimiento')->fetchAll($query);		
		}
		
		public function queryStockIncorrectos()
		{
				$query = $this->getTable('TblReferencia')
					->select()
					->where('loc1 < 0 OR loc2 < 0 OR loc3 < 0 OR loc4 < 0 OR loc5 < 0')
					->order(array('calidad', 'color' , 'tipoenvase'));
			return $this->getTable('TblReferencia')->fetchAll($query);	
		}
		
		public function getTienda($idDeposito)
		{
			$res=$this->queryID('TblStockDeposito',$idDeposito);
			if (isset($res))
			{
				return $res['tienda'];
			}
			else
			{
				return 'NO TIENDA';
			}
		}
		
		public function queryViewLocations()
		{
			$depositos = $this->getTable('TblStockDeposito')->selectAll()->toArray();
			
			$res = array();
			foreach ($depositos as $deposito)
			{
				$column = 'loc' . $deposito['idDeposito'];
				$res[$column] = $deposito['tienda'];
			}
			return $res;
		}
		
		public function queryTotals()
		{
			$query = $this->getTable('TblReferencia')->select()
					->from( array('TblReferencia' => 'TblReferencia' ), 
							array('totalloc1' => new Zend_Db_Expr('SUM(cast(case when (tipoenvase > 1 ) then tipoenvase*precio*loc1 else precio*loc1 end as decimal) ) '),
								'totalloc2' => new Zend_Db_Expr('SUM(cast(case when (tipoenvase > 1 ) then tipoenvase*precio*loc2 else precio*loc2 end as decimal) )  '),
								'totalloc3' => new Zend_Db_Expr('SUM(cast(case when (tipoenvase > 1 ) then tipoenvase*precio*loc3 else precio*loc3 end as decimal) ) '),
								'totalloc4' => new Zend_Db_Expr('SUM(cast(case when (tipoenvase > 1 ) then tipoenvase*precio*loc4 else precio*loc4 end as decimal) ) '),
								'totalloc5' => new Zend_Db_Expr('SUM(cast(case when (tipoenvase > 1 ) then tipoenvase*precio*loc5 else precio*loc5 end as decimal) ) ')));
				return $this->getTable('TblReferencia')->fetchRow($query);
		}
/*		SELECT SUM(cast(case when (tipoenvase > 1 ) then tipoenvase*precio*loc1 else precio*loc1 end as decimal) ) as total1 ,
SUM(cast(case when (tipoenvase > 1 ) then tipoenvase*precio*loc2 else precio*loc2 end as decimal) ) as total2 ,
SUM(cast(case when (tipoenvase > 1 ) then tipoenvase*precio*loc3 else precio*loc3 end as decimal) ) as total3 ,
SUM(cast(case when (tipoenvase > 1 ) then tipoenvase*precio*loc4 else precio*loc4 end as decimal) ) as total4 ,
SUM(cast(case when (tipoenvase > 1 ) then tipoenvase*precio*loc5 else precio*loc5 end as decimal) ) as total5 from tblreferencia;
*/		
	}
?>