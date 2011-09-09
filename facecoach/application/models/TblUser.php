<?php

	class TblUser {
		/* the dbtable model */
		protected $_table,$_tableCoachers;

		public function getTable() {
			/* initialize the table model */
			if($this->_table === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblUser.php');
				$this->_table = new DbTable_TblUser();
			}
			return $this->_table;
		}
	
		public function getTableCoacher() {
			/* initialize the table model */
			if($this->_tableCoachers === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblCoacher.php');
				$this->_tableCoachers = new DbTable_TblCoacher();
			}
			return $this->_tableCoachers;
		}

		public function getAllUsers() {
			$select = $this->getTable()->select()->group('username')->order('username');
			return $this->getTable()->fetchAll($select);
		}
		
		public function getUserKey($key) {
			$select = $this->getTable()->select()->where('username = ?' , $key);
			return $this->getTable()->fetchAll($select);
		}
	
		
		public function  getCoachersUserKey2($idUser,$scope)
		{
			// select firstname, lastname, idUser from tbluser where userprofile = '1' join tblcoacher on
			// tblcoacher.idcoacher = tbluser.idcoacher and tblcoacher.iduser = $iduser
			$compare = ' ';
			if ($scope = '0')
			{
				$compare .= '!';
			}
			$compare .= '= \'' . $idUser . '\'';
			
			$select = $this->getTable()->select()
						->from(array('TblUser' => 'TblUser' , 
							array('lastname','firstname','idUser') ) )
					->join(array('coachers' => 'TblCoacher'), 'coachers.idcoacher = tbluser.idcoacher and coachers.iduser ' . $compare )
					->setIntegrityCheck(false);
			return $this->getTable()->fetchAll($select);
		}
		
		
		public function  getCoachersUserKey($idUser,$scope)
		{
			// select firstname, lastname, idUser from tbluser where userprofile = '1' join tblcoacher on
			// tblcoacher.idcoacher = tbluser.idcoacher and tblcoacher.iduser = $iduser
			$compare = ' ';
			if ($scope == '1')
			{
			
				$compare .= '= \'' . $idUser . '\'';
			
				$select = $this->getTable()->select()
						->from(array('TblUser' => 'TblUser' , 
							array('lastname','firstname','idUser') ) )
					->join(array('coachers' => 'TblCoacher'), 'coachers.idcoacher = tbluser.idUser and coachers.idUser ' . $compare )
					->setIntegrityCheck(false);
				return $this->getTable()->fetchAll($select);
			}
			else
			{

			
				$select = $this->getTable()->select()
						->from(array('TblUser' => 'TblUser' , 
							array('lastname','firstname','idUser') ) )
					->where( 'tblUser.idUser != \'' . $idUser . '\' AND tblUser.userprofile = \'1\'' );
				return $this->getTable()->fetchAll($select);
			}
		}
		
		
		public function  getUsersPerCoacher($idCoacher)
		{
			// select firstname, lastname, idUser from tbluser where userprofile = '1' join tblcoacher on
			// tblcoacher.idcoacher = tbluser.idcoacher and tblcoacher.iduser = $iduser
			
			$compare = '= \'' . $idCoacher . '\'';
			
			$select = $this->getTable()->select()
						->from(array('TblUser' => 'TblUser' , 
							array('lastname','firstname','idUser') ) )
					->join(array('coachers' => 'TblCoacher'), 'coachers.idUser = TblUser.idUser and coachers.idCoacher ' . $compare )
					->setIntegrityCheck(false);
				return $this->getTable()->fetchAll($select);
		}
		
		public function linkCoacher($idUser,$idCoacher)
		{
			$select = $this->getTableCoacher()->select()->where('idUser = \'' . $idUser . '\' AND idCoacher = \'' . $idCoacher . '\'' );
			$results = $this->getTableCoacher()->fetchAll($select);
			if (count($results) > 0 )
			{
				return;
			}
			$data = array('idUser'=>$idUser,'idCoacher'=>$idCoacher);
			$this->getTableCoacher()->insert($data);
		}
		
		public function unlinkCoacher($idUser,$idCoacher)
		{
			$where = $this->getTableCoacher()->getAdapter()->quoteInto('idUser = \'' . $idUser . '\' AND idCoacher = \'' . $idCoacher . '\'' );

			/* delete user */
			return $this->getTableCoacher()->delete($where);
		}
		
		public function get($idUser) {
			$select = $this->getTable()->select()->where('idUser = ?' , $idUser);
			return $this->getTable()->fetchRow($select);
		}
		
		public function createUser($username,$pass,$firstname,$lastn){
			$u = $this->getTable()->fetchRow($this->getTable()->select()->where('username=?',$username));
			if ($u==null) {
				$data = array('username'=>$username,'password'=>$pass,'firstname'=>$firstname,'lastname'=>$lastn);
				$this->getTable()->insert($data);
				
				$u = $this->getTable()->fetchRow($this->getTable()->select()->where('username=?',$username));
				if ($u==null)
				{
					return 0;
				}
				else
				{
					return $u['idUser'];
				}
			}
			else {
				return 0;
			}
		}
		
		public function update(array $data, $id) {
			// A jokin le falta esto
			/* check if all the posted fields are valid db fiels */
			$fields = $this->getTable()->info(Zend_Db_Table_Abstract::COLS);
			foreach($data as $field => $value) {
				if(!in_array($field, $fields)) {
					unset($data[$field]);
				}
			}

			$where = $this->getTable()->getAdapter()->quoteInto('idUser = ?', $id);

			$res = $this->getTable()->update($data, $where);
			return $res;		
		}
		
	}
?>