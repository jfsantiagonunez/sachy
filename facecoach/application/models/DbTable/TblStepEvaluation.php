<?php

	class DbTable_TblStepEvaluation extends Zend_Db_Table_Abstract {
		/* table name */
		protected $_name = 'TblStepEvaluation';

		public function getName() {
			return $this->_name;
		}
		
		
		public function getPK() {
			return 'keyStep';
		}
		
		public function getFK() {
			return 'idEvaluation';
		}
	}
?>