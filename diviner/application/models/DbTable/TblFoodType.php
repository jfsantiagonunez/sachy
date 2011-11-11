<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblFoodType extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblFoodType';
		protected $_pk = 'foodType';
		protected $_fk = '';
		protected $_id = 'idFoodType';

	
	}
?>