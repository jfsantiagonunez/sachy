<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblEventType extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblEventType';
		protected $_pk = 'eventType';
		protected $_fk = '';
		protected $_id = 'idEventType';

	
	}
?>