<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class DbTable_TblSetting extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblSetting';
		protected $_pk = 'idKey';
		protected $_fk = '';
		protected $_id = 'idKey';

		
	}
?>