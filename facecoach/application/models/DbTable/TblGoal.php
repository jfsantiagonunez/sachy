<?php

	class DbTable_TblGoal extends Zend_Db_Table_Abstract {
		/* table name */
		protected $_name = 'TblGoal';

		public function getName() {
			return $this->_name;
		}
	}
?>