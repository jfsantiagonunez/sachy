<?php

	class TblLogDairy {
		/* the dbtable model */
		protected $_table;

		public function getTable() {
			/* initialize the table model */
			if($this->_table === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblLogDairy.php');
				$this->_table = new DbTable_TblLogDairy();
			}
			return $this->_table;
		}
		
		public function getLogsPerUserKey($key,$date) {
			$select = $this->getTable()->select()->where('idUser = ' . $key . ' AND ' . ' logDate > ' . $date)->order('logDate DESC');
			return $this->getTable()->fetchAll($select);
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
			$where = $this->getTable()->getAdapter()->quoteInto('idTblLogDairy = ?', $id);

			/* delete user */
			return $this->getTable()->delete($where);
		}
	}
?>