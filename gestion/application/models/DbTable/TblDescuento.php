<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class DbTable_TblDescuento extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblDescuento';
		protected $_pk = 'descuento';
		protected $_fk = 'idCliente';
		protected $_id = 'idDescuento';
		protected $_NumeroPk = 2;
		protected $_pk2 = 'idCliente';
		
	}
?>