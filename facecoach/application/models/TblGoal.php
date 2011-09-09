<?php

	class TblGoal {
		/* the dbtable model */
		protected $_table;

		public function getTable() {
			/* initialize the table model */
			if($this->_table === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblGoal.php');
				$this->_table = new DbTable_TblGoal();
			}
			return $this->_table;
		}
		
		public function getCount($key) 
    	{
			$select = $this->getTable()->select()->from($this->getTable(), array('count(*) as amount'))->where($key );
        	$rows = $this->getTable()->fetchAll($select);
        	return($rows[0]->amount);
    	}
    	
		public function getStats($key)
		{
			$stats = array();
			$stats['count'] = $this->getCount($key);
			return $stats;
		}
		
		
		public function getGoalsPerUserKey($key) {
			$select = $this->getTable()->select()->where('idUser = ?' , $key);
			return $this->getTable()->fetchAll($select);
		}
		
		
		public function get($id){
			$select = $this->getTable()->select()->where("idGoal = ?",$id);
			return $this->getTable()->fetchRow($select);
		}
		
		public function getTitle($id)
		{
			$goal = $this->get($id)->toArray();
			return $goal['titleGoal'];
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
			$where = $this->getTable()->getAdapter()->quoteInto('idGoal = ?', $id);

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

			$where = $this->getTable()->getAdapter()->quoteInto('idGoal = ?', $id);

			$res = $this->getTable()->update($data, $where);
			return $res;
		}
}
	
?>