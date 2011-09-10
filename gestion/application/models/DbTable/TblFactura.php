<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class DbTable_TblFactura extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblFactura';
		protected $_pk = 'numerofactura';
		protected $_fk = 'idCliente';
		protected $_id = 'idFactura';

	
	}
?>
