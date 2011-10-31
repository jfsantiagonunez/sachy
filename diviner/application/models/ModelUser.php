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
					->order('TblUser.username');
			return $this->getTable($tableId)->fetchAll($query);	
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