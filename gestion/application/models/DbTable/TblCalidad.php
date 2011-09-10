<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class DbTable_TblCalidad extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblCalidad';
		protected $_pk = 'calidad';
		protected $_fk = '';
		protected $_id = 'calidad';

		
	}
?>