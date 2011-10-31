<?php

require_once(APPLICATION_PATH . '/models/ModelBase.php');

	class ModelWine extends ModelBase {
		/* the dbtable model */
		protected $_table;
		protected $_tabledeposito;
		

		public function getTable($tablename) {
			/* initialize the table model */
			if ( ($tablename == 'TblWine') ||  ($tablename == 'wineId') )
			{
				if($this->_table === null) {
					require_once(APPLICATION_PATH . '/models/DbTable/TblWine.php');
					$this->_table = new DbTable_TblWine();
				}

				return $this->_table;
			}
		
		}
		
		
		public function queryWines($searchwine=null)
		{
			if (isset($searchwine))
			{
				return $this->query('TblWine',$searchwine);
			}
			else
			{
				return $this->queryAll('TblWine');
			}	
		}
						
		public function get($key)
		{
			return $this->getUserKey($key);
		}
		
		public function getUserKey($key) {
			return $this->getTable('TblUser')->selectID($key);
		}
	
		public function createUser($data)
		{
			return $this->getTable('TblUser')->insertData($data);
		}
		
		public function updateUser(array $data, $id) {
			return $this->getTable('TblUser')->updateData($data,$id);
		}
			
		public function importWine(array $data)
		{
			$this->create('TblWine',$data);
		}
	}
?>