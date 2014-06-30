
<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblDiary extends TblBase {
		/* table name */
		protected $_name = 'TblDiary';
		protected $_pk = 'idTblDiary';
		protected $_fk = '';
		protected $_id = 'idTblDiary';
		protected $_checkduplicateswhencreate = 0;
	    protected $_textField = 'diary';
	}
?>