<?php

	class DbTable_TblCoacher extends Zend_Db_Table_Abstract {
		/* table name */
		protected $_name = 'TblCoacher';

		public function getName() {
			return $this->_name;
		}
	}
?>