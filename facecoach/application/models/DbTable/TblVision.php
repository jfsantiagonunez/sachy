<?php

	class DbTable_TblVision extends Zend_Db_Table_Abstract {
		/* table name */
		protected $_name = 'TblVision';

		public function getName() {
			return $this->_name;
		}
	}
?>