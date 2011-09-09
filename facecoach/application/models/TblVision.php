<?php

	class TblVision {
		/* the dbtable model */
		protected $_table;

		public function getTable() {
			/* initialize the table model */
			if($this->_table === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblVision.php');
				$this->_table = new DbTable_TblVision();
			}
			return $this->_table;
		}
	

		public function getVisionForUserId($key) {
			$select = $this->getTable()->select()->where('idUser = ?' , $key);
			return $this->getTable()->fetchRow($select);
		}
		
		public function getVisionTitlePerUserKey($key)
		{
			$mision = $this->getVisionForUserId($key);
			if (isset($mision))
			{
				//$goal = $mission->toArray();
				return $mision['descVision'];
			}
			else
				return 'You have not defined your mission yet';
		}
		
		public function insert(array $data) {
			/* check if all the posted fields are valid db fiels */
			$fields = $this->getTable()->info(Zend_Db_Table_Abstract::COLS);
			foreach($data as $field => $value) {
				if(!in_array($field, $fields)) {
					unset($data[$field]);
				}
			}
			try{
				$res = $this->getTable()->insert($data);
				return $res;
			}
			catch(exception $e){
				return $e;
			}	
		}
		
		public function delete($id){
			
			/* create where clause */
			$where = $this->getTable()->getAdapter()->quoteInto('idStrategy = ?', $id);

			/* delete user */
			return $this->getTable()->delete($where);
		}
		
		public function updateVisionForUserKey(array $data, $id) {
			/* check if all the posted fields are valid db fiels */
			$fields = $this->getTable()->info(Zend_Db_Table_Abstract::COLS);
			foreach($data as $field => $value) {
				if(!in_array($field, $fields)) {
					unset($data[$field]);
				}
			}

			$where = $this->getTable()->getAdapter()->quoteInto('idUser = ?', $id);

			$res = $this->getTable()->update($data, $where);
			return $res;
		}
	
	}
?>