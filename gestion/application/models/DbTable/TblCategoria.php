<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class DbTable_TblCategoria extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblCategoriaProducto';
		protected $_pk = 'categoria';
		protected $_fk = '';
		protected $_id = 'idCategoria';
	
	}
?>