
<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblLeitmotif extends TblBase {
		/* table name */
		protected $_name = 'TblLeitmotif';
		protected $_pk = 'idLeitmotif';
		protected $_fk = '';
		protected $_id = 'idLeitmotif';
		protected $_checkduplicateswhencreate = 0;
		protected $_textField = 'leitmotif';
	}
?>