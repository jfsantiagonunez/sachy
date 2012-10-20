<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblVentaCaja extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblVentaCaja';
		protected $_pk = 'idTblVentaCaja';
		protected $_fk = 'idAlbaran';
		protected $_id = 'idTblVentaCaja';

		
	}
?>