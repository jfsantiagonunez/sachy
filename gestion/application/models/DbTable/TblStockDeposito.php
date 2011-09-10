<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class DbTable_TblStockDeposito extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblStockDeposito';
		protected $_pk = 'tienda';
		protected $_fk = 'idCliente';
		protected $_id = 'idDeposito';

		
	}
?>