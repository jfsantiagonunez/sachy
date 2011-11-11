<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblEventPeople extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblEventPeople';
		protected $_pk = 'idEventType';		
		protected $_pk2 = 'idPeopleType';
		protected $_NumeroPk = 2;
		protected $_fk = '';
		protected $_id = 'idEventPeople';

	
	}
?>