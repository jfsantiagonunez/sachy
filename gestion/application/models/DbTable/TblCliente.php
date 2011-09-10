<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class DbTable_TblCliente extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblCliente';
		protected $_pk = 'nombre';
		protected $_fk = '';
		protected $_id = 'idCliente';

	
	}
?>