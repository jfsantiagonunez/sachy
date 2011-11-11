<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblFoodWine extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblFoodWine';
		protected $_pk = 'idFoodType';		
		protected $_pk2 = 'idWineType';
		protected $_NumeroPk = 2;
		protected $_fk = '';
		protected $_id = 'idFoodWine';

	
	}
?>