<?php

	class DbTable_TblStepEvaluationDict extends Zend_Db_Table_Abstract {
		/* table name */
		protected $_name = 'TblStepEvaluationDict';

		public function getName() {
			return $this->_name;
		}
		
		public function getPK() {
			return 'idStep';
		}
		
	}
?>