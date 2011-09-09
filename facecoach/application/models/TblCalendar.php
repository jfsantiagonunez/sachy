<?php

	class TblCalendar {
		/* the dbtable model */
		protected $_table;

		public function getTable() {
			/* initialize the table model */
			if($this->_table === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblCalendar.php');
				$this->_table = new DbTable_TblCalendar();
			}
			return $this->_table;
		}

		public function createCalendar($data){
			$u = $this->getTable()->fetchRow($this->getTable()->select()->where('email=?',$data['email']));
			if ($u==null) {
				/* check if all the posted fields are valid db fiels */
				$fields = $this->getTable()->info(Zend_Db_Table_Abstract::COLS);
				foreach($data as $field => $value) {
				if(!in_array($field, $fields)) {
					unset($data[$field]);
					}
				}
				try{
					$res = $this->getTable()->insert($data);
					return 1;
				}
				catch(exception $e){
					return 0;
				}	
			}
			else {
				return 0;
			}
		}
		
		public function getCalendarsPerUserKey($key) {
			$select = $this->getTable()->select()->where('idUser = ?' , $key);
			return $this->getTable()->fetchAll($select);
		}
	
		public function getAllCalendarsToArray($key)
		{
			$data = $this->getCalendarsPerUserKey($key);
			$res = array();
			foreach ($data as $id)
			{
				$res[$id['idCalendar']] = $id['title'];
			}
			return $res;
		}
		
		public function get($id){
			$select = $this->getTable()->select()->where("idCalendar = ?",$id);
			return $this->getTable()->fetchRow($select);
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
			$where = $this->getTable()->getAdapter()->quoteInto('idCalendar = ?', $id);

			/* delete user */
			return $this->getTable()->delete($where);
		}
		
		public function update(array $data, $id) {
			/* check if all the posted fields are valid db fiels */
			$fields = $this->getTable()->info(Zend_Db_Table_Abstract::COLS);
			foreach($data as $field => $value) {
				if(!in_array($field, $fields)) {
					unset($data[$field]);
				}
			}

			$where = $this->getTable()->getAdapter()->quoteInto('idCalendar = ?', $id);

			$res = $this->getTable()->update($data, $where);
			return $res;
		}
}
	
?>