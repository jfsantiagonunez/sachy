<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblWineType extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblWineType';
		protected $_pk = 'wineType';
		protected $_fk = '';
		protected $_id = 'idWineType';

	
	}
?>