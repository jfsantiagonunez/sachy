<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblGrape extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblGrapeType';
		protected $_pk = 'grapeName';
		protected $_fk = '';
		protected $_id = 'idGrape';

	
	}
?>