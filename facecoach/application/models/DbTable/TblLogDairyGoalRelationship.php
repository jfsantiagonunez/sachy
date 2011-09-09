<?php

	class DbTable_TblLogGoalRelationship extends Zend_Db_Table_Abstract {
		/* table name */
		protected $_name = 'TblLogGoalRelationship';

		public function getName() {
			return $this->_name;
		}
	}
?>