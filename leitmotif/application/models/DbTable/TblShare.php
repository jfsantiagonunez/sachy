
<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblShare extends TblBase {
		/* table name */
		protected $_name = 'TblShare';
		protected $_pk = 'idShare';
		protected $_fk = '';
		protected $_id = 'idShare';
		protected $_checkduplicateswhencreate=0;
	
	}
?>