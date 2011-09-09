<?php

	class DbTable_TblEvaluation extends Zend_Db_Table_Abstract {
		/* table name */
		protected $_name = 'TblEvaluation';

		public function getName() {
			return $this->_name;
		}
		
		public function getPK() {
			return 'idEvaluation';
		}
		
		public function getFK() {
			return 'idGoal';
		}
		
		public function getFK2() {
			return 'idUser';
		}
	}
?>