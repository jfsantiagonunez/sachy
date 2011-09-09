<?php

	class TblLogDairyGoalRelationship {
		/* the dbtable model */
		protected $_table;

		public function getTable() {
			/* initialize the table model */
			if($this->_table === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblLogDairyGoalRelationship.php');
				$this->_table = new DbTable_TblLogDairyGoalRelationship();
			}
			return $this->_table;
		}
	}

	/*	public function SearchNameByKeyword($keyword) {
			$select = $this->getTable()->select()->where('NAME = ?' , $keyword)->group('NAME')->order('NAME');
			return $this->getTable()->fetchAll($select);
		}
		
		public function SearchLoosyNameByKeyword($keyword) {
			$nk = $keyword . "%";
			
			$col = $this->getTable()->getAdapter()->quoteIdentifier('NAME');
			$where = $this->getTable()->getAdapter()->quoteInto("$col LIKE ?", $nk);
			
			$select = $this->getTable()->select()->where($where)->group('NAME')->order('NAME');
			
			return $this->getTable()->fetchAll($select);
		}*/
	
?>