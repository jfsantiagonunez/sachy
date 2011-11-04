
<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblVinculo extends TblBase {
		/* table name */
		protected $_name = 'TblVinculo';
		protected $_pk = 'idShare';
		protected $_pk2 = 'idUser';
		protected $_NumeroPk = 2;
		protected $_fk = '';
		protected $_id = 'idVinculo';

	
	}
?>