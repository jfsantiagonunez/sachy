<?php

	class DbTable_TblEvent extends Zend_Db_Table_Abstract {
		/* table name */
		protected $_name = 'TblEvent';

		public function getName() {
			return $this->_name;
		}
	}
?>