<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblRegion extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblRegion';
		protected $_pk = 'region';
		protected $_fk = '';
		protected $_id = 'idRegion';

	
	}
?>