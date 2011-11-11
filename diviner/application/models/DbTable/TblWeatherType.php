<?php
	require_once(APPLICATION_PATH . '/models/DbTable/TblBase.php');
	
	class TblWeatherType extends DbTable_TblBase {
		/* table name */
		protected $_name = 'TblWeatherType';
		protected $_pk = 'weatherType';
		protected $_fk = '';
		protected $_id = 'idWeatherType';
	}
?>