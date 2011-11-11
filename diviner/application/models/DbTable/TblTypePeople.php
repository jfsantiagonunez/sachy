<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblTypePeople extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblTypePeople';
		protected $_pk = 'peopleType';
		protected $_fk = '';
		protected $_id = 'idPeopleType';

	
	}
?>