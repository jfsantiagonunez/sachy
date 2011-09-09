<?php

	class DbTable_TblUser extends Zend_Db_Table_Abstract {
		/* table name */
		protected $_name = 'TblUser';

		public function getName() {
			return $this->_name;
		}
	}
?>