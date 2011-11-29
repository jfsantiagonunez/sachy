<?php

	class TblBase extends Zend_Db_Table_Abstract {
		
		/* table name */
		protected $_name = 'TblBase';
		protected $_pk = 'pk';  // unique name
		protected $_NumeroPk = 1;
		protected $_fk = 'fk';  // external reference
		protected $_id = 'id';  //internal Id
		protected $_checkduplicateswhencreate = 1;
		
		public function getName() {
			return $this->_name;
		}
		
		public function getPK() {
			return $this->_pk; 
		}
		
		public function getFK() {
			return $this->_fk; 
		}

		public function getId() {
			return $this->_id; 
		}
		
		public function selectID($id){
			$select = $this->select()->where( $this->_id . ' = ?',$id);
			return $this->fetchRow($select);
		}
		
		public function selectPK($id){
			$select = $this->select()->where( $this->_pk . ' = ?',$id);
			return $this->fetchRow($select);
		}
		
		
		public function selectPKs(array $id){
			$where = $this->_pk . ' = \'' . $id[$this->_pk] . '\'';
			
			if ($this->_NumeroPk > 1 )
			   $where .= ' AND ' . $this->_pk2 . ' = \'' . $id[$this->_pk2] . '\'';
			if ($this->_NumeroPk > 2 )
			   $where .= ' AND ' . $this->_pk3 . ' = \'' . $id[$this->_pk3] . '\'';
			
			$select = $this->select()->where( $where );

			return $this->fetchRow($select);
		}
		
		public function selectFK($id){
			$select = $this->select()->where( $this->_fk . ' = ?',$id);
			return $this->fetchAll($select);
		}
		
		public function selectWhere($where){
			$select = $this->select()->where( $where );
			return $this->fetchAll($select);
		}
		
		public function selectAll(){
			$select = $this->select();
			return $this->fetchAll($select);
		}
		
		public function updateData(array $data, $id, $fieldwhere = null ) {
			$fields = $this->info(Zend_Db_Table_Abstract::COLS);
			foreach($data as $field => $value) {
				if(!in_array($field, $fields)) {
					unset($data[$field]);
				}
			}
			if (!isset($fieldwhere))
			{
				$fieldwhere = $this->_id;

			}

			$where = $this->getAdapter()->quoteInto( $fieldwhere . ' = ?', $id);

			return $this->update($data, $where);
		
		}
		
		public function updateDataWhere(array $data, $where ) {
			$fields = $this->info(Zend_Db_Table_Abstract::COLS);
			foreach($data as $field => $value) {
				if(!in_array($field, $fields)) {
					unset($data[$field]);
				}
			}
			if (!isset($fieldwhere))
			{
				$fieldwhere = $this->_id;

			}

			$where = $this->getAdapter()->quoteInto( $where);

			return $this->update($data, $where);
		
		}
		
		
		
		// Returns Id if succesfull
		public function insertData(array $data)
	    {
			
			if ($this->_checkduplicateswhencreate == 1)
			{
				$exists = $this->selectPKs($data);
				if ($exists != null)
				{
					//echo 'YA EXISTE';
					return null;
				}
			}
			$data[$this->_id] = $this->uuid();
			/* check if all the posted fields are valid db fiels */
			$fields = $this->info(Zend_Db_Table_Abstract::COLS);
			foreach($data as $field => $value) {
				if(!in_array($field, $fields)) {
					unset($data[$field]);
				}
			}
			try{
				$lastid = $this->insert($data);

				return $lastid;
				
			}
			catch(exception $e){
				print $e;
				return $e;
			}	
		}
		
		public function deleteData($id){
			
			/* create where clause */
			$where = $this->getAdapter()->quoteInto($this->_id . ' = ?', $id);

			return $this->delete($where);
		}
		
		function uuid($prefix = '')
		{
			$chars = md5(uniqid(mt_rand(), true));
			$uuid  = substr($chars,0,8) . '-';
			$uuid .= substr($chars,8,4) . '-';
			$uuid .= substr($chars,12,4) . '-';
			$uuid .= substr($chars,16,4) . '-';
			$uuid .= substr($chars,20,12);
			//print($uuid . '<br/>');
			return $prefix . $uuid;
		}
		
	}
?>