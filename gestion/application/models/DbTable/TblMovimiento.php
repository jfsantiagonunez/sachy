<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class DbTable_TblMovimiento extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblMovimiento';
		protected $_pk = 'idReferencia';
		protected $_fk = '';
		protected $_id = 'idMovimiento';
		protected $_checkduplicateswhencreate = 0;
		protected $_NumeroPk = 2;
		protected $_pk2 = 'cantidad';
		protected $_pk3 = 'idAlbaran';
		

	}
?>