<?php

require_once(APPLICATION_PATH . '/models/ModelBase.php');

	class ModelUser extends ModelBase {
		/* the dbtable model */
		protected $_table;
		protected $_tabledeposito;
		

		public function getTable($tablename) {
			/* initialize the table model */
		if ( ($tablename == 'TblUser') ||  ($tablename == 'idUser') )
		{
			if($this->_table === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblUser.php');
				$this->_table = new DbTable_TblUser();
			}

			return $this->_table;
		}
		else if ( ($tablename == 'TblStockDeposito') ||  ($tablename == 'idDeposito') )
		{
			if($this->_tabledeposito === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblStockDeposito.php');
				$this->_tabledeposito = new DbTable_TblStockDeposito();
			}

			return $this->_tabledeposito;
		}
		}
		
		
		public function queryUser($idUser=null)
		{
			$tableId = 'TblUser';
			$where = '';
			if (isset($idUser))
			{
				$where = ' AND TblUser.idUser = \'' . $idUser .'\'';
			}
			$query = $this->getTable($tableId)
					->select()
					->from(array($tableId => $tableId ) )
					->join(array('deps' => 'TblStockDeposito'), 'deps.idDeposito=TblUser.idDeposito ' . $where , array('tienda') )
					->order('TblUser.username')
					->setIntegrityCheck(false);
			return $this->getTable($tableId)->fetchAll($query);	
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
		
						
		public function get($key)
		{
			return $this->getUserKey($key);
		}
		
		public function getUserKey($key) {
			return $this->getTable('TblUser')->selectID($key);
		}
	
		public function createUser($data)
		{
			return $this->getTable('TblUser')->insertData($data);
		}
		
		public function updateUser(array $data, $id) {
			return $this->getTable('TblUser')->updateData($data,$id);
		}
			
	}
?>