<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblUser extends TblBase {
		/* table name */
		protected $_name = 'TblUser';
		protected $_pk = 'username';
		protected $_fk = '';
		protected $_id = 'idUser';

	
	}
?>