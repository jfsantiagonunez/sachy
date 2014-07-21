
<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblShare extends TblBase {
		/* table name */
		protected $_name = 'TblShared';
		protected $_pk = 'fkidLeitmotif';
		protected $_fk = 'idSharedUser';
		protected $_id = 'fkidLeitmotif';
		protected $_checkduplicateswhencreate=0;
	
	}
?>