<?php

	class DbTable_TblTask extends Zend_Db_Table_Abstract {
		/* table name */
		protected $_name = 'TblTask';

		public function getName() {
			return $this->_name;
		}
	}
?>