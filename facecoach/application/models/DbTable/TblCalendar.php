<?php

	class DbTable_TblCalendar extends Zend_Db_Table_Abstract {
		/* table name */
		protected $_name = 'TblCalendar';

		public function getName() {
			return $this->_name;
		}
	}
?>