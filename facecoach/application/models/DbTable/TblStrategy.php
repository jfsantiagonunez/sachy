<?php

	class DbTable_TblStrategy extends Zend_Db_Table_Abstract {
		/* table name */
		protected $_name = 'TblStrategy';

		public function getName() {
			return $this->_name;
		}
	}
?>