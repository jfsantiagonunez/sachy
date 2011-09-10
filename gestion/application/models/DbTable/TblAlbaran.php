<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class DbTable_TblAlbaran extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblAlbaran';
		protected $_pk = 'numeroalbaran';
		protected $_fk = 'idCliente';
		protected $_id = 'idAlbaran';

		protected $_checkduplicateswhencreate = 0;
	
	}
?>