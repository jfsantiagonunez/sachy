<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class DbTable_TblReferencia extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblReferencia';
		protected $_pk = 'calidad';
		protected $_fk = '';
		protected $_id = 'idReferencia';
		protected $_NumeroPk = 3;
		protected $_pk2 = 'color';
		protected $_pk3 = 'tipoenvase';
	}
?>