<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblCountry extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblCountry';
		protected $_pk = 'Country';
		protected $_fk = '';
		protected $_id = 'idCountry';

	
	}
?>