<?php

require_once(APPLICATION_PATH . '/models/ModelBase.php');

class ModelLeitmotif extends ModelBase {

	protected $_table;
	protected $_tablemotif;
	
	protected $_TblVinculo;
	protected $_TblShare;
	protected $_TblDiary;

	public function getTable($tablename) {

		if ( ($tablename == 'TblUser') ||  ($tablename == 'idUser') )
		{
			if($this->_table === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblUser.php');
				$this->_table = new TblUser();
			}

			return $this->_table;
		}
		else if ( ($tablename == 'TblLeitmotif') ||  ($tablename == 'idLeitmotif') )
		{
			if($this->_tablemotif === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblLeitmotif.php');
				$this->_tablemotif = new TblLeitmotif();
			}

			return $this->_tablemotif;
		}
		else if ( ($tablename == 'TblDiary') ||  ($tablename == 'idTblDiary') )
		{
			if($this->_TblDiary === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblDiary.php');
				$this->_TblDiary = new TblDiary();
			}

			return $this->_TblDiary;
		}
		else if ( ($tablename == 'TblShare') ||  ($tablename == 'idShare') )
		{
			if($this->_TblShare === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblShare.php');
				$this->_TblShare = new TblShare();
			}
				
			return $this->_TblShare;
		}
		else if ( ($tablename == 'TblVinculo') ||  ($tablename == 'idVinculo') )
		{
			if($this->_TblVinculo === null) {
				require_once(APPLICATION_PATH . '/models/DbTable/TblVinculo.php');
				$this->_TblVinculo = new TblVinculo();
			}
				
			return $this->_TblVinculo;
		}
	}
		
	public function queryUser($idUser=null)
	{
		$tableId = 'TblUser';
		$where = '';
		if (isset($idUser))
		{
			$where = ' AND TblUser.idUser = \'' . $idUser .'\'';
		}
		$query = $this->getTable($tableId)
		->select()
		->from(array($tableId => $tableId ) )
		->order('TblUser.username');
		return $this->getTable($tableId)->fetchAll($query);
	}

//	public function queryLeitmotifPerUser($idParent,$idUser)
//	{
//			
//		$tableId = 'TblLeitmotif';
//			
//		$where = 'lm.level = \''.$level.'\' ';
//		$where .= 'AND vincu.idUser = \''.$idUser.'\'';
//			
//		$query = $this->getTable($tableId)
//		->select()
//		->from(array('lm' => $tableId ),
//		array('idLeitmotif','leitmotif','complete','urgency','owner','complete','urgency','priority','importance',
//						'idShare','idUp','level','fecha') )
//		->join(array('vincu' => 'TblVinculo'), 'vincu.idShare = lm.idShare AND '. $where , array('permiso','profile') )
//		->order('lm.priority')
//		->setIntegrityCheck(false);
//			
//		return $this->getTable($tableId)->fetchAll($query);
//	}

	public function queryLeitmotifPerUser($idParent,$idUser)
	{
			
		$tableId = 'TblLeitmotif';

		$where = 'idParent = \''.$idParent.'\' ';
		$where .= 'AND idUser = \''.$idUser.'\'';
		//return $this->queryWhere($tableId,$where);
		
		$query = $this->getTable($tableId)
		->select()
		->from(array('lm' => $tableId ),
		array('*',' substr(leitmotif,1,50) as title'))
		->where($where)
		->order('fecha asc');
		return $this->getTable($tableId)->fetchAll($query);
	}

	public function queryDiaryPerUser($idParent,$idUser)
	{
			
		$tableId = 'TblDiary';

		$where = 'idParent = \''.$idParent.'\' ';
		$where .= 'AND idUser = \''.$idUser.'\'';

		$query = $this->getTable($tableId)
		->select()
		->from(array('diary' => $tableId ),
		array('*',' substr(diary,1,50) as title'))
		->where($where)
		->order('fecha desc');
		return $this->getTable($tableId)->fetchAll($query);
	}
	
	
	public function createTopic($table,array $data)
	{
		$TblTable = $this->getTable($table);
		$data[$TblTable->getPK()] = uniqid();
		$data['fecha'] = date('Y-m-d h:m:s');
		$data[$TblTable->getTextField()] = urldecode($data['texttopic']);
		unset($data['texttopic']);
        $this->create($table, $data);
	}
	
	public function queryUsersSharingWithUser($idUser)
	{
		$query = $this->getTable('TblShare')->select()
				->from(	array('Share' => 'TblShared' ),
				array('idSharedUser','fkidLeitmotif','idOwner'))
				->join(array('User' => 'TblUser'),
						'Share.idOwner = User.idUser AND Share.idSharedUser = \''.$idUser.'\'' ,
						array('username','firstname','lastname as title' ) )
						->order('username asc')
						->setIntegrityCheck(false);
		return $this->getTable('TblShare')->fetchAll($query);
	}
	
	public function querySharedLeitmotifPerUser($idParent,$idUser)
	{
		$query = $this->getTable('TblShare')->select()
				->from(	array('Share' => 'TblShared' ),
				array('idSharedUser','fkidLeitmotif'))
				->join(array('Leitmotif' => 'TblLeitmotif'),
						'Share.fkidLeitmotif = Leitmotif.idLeitmotif AND Share.idSharedUser = \''.$idUser.'\''.
				        'AND Share.idOwner = \''.$idParent.'\'' ,
						array('*',' substr(leitmotif,1,50) as title'))
						->order('fecha asc')
						->setIntegrityCheck(false);
		return $this->getTable('TblShare')->fetchAll($query);
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
		
}
?>