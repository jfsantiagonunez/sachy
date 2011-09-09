<?php

	class DbTable_TblLogDairy extends Zend_Db_Table_Abstract {
		/* table name */
		protected $_name = 'TblLogDairy';

		public function getName() {
			return $this->_name;
		}
	}
?>

