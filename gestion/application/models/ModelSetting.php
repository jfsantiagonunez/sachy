<?php

require_once(APPLICATION_PATH . '/models/ModelBase.php');


	class ModelSetting extends ModelBase {
		/* the dbtable model */
		protected $_tabla;
		protected $_tableId = 'TblSetting';

		public function getTable($tablename) {

			if($this->_tabla === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblSetting.php');
				$this->_tabla = new DbTable_TblSetting();
			}

			return $this->_tabla;
	
		}
		
	}
?>