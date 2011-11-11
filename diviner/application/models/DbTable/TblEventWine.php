<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblEventWine extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblEventWine';
		protected $_pk = 'idEventType';		
		protected $_pk2 = 'idWineType';
		protected $_NumeroPk = 2;
		protected $_fk = '';
		protected $_id = 'idEventWine';

	
	}
?>