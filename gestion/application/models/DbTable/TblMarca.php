<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class DbTable_TblMarca extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblMarca';
		protected $_pk = 'marca';
		protected $_fk = '';
		protected $_id = 'idMarca';

		
	}
?>