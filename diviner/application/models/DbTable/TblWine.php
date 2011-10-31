<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class DbTable_TblWine extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblWine';
		protected $_pk = 'winename';
		protected $_fk = '';
		protected $_id = 'wineId';

	
	}
?>