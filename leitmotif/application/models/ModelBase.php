<?php

class ModelBase {
	/* the dbtable model */



	//Define the getTable in the TblBase derived class
	public function getTableId()
	{
		return $this->_tableId;
	}

	public function queryID($tableId,$key)
	{
		return $this->getTable($tableId)->selectID($key);
	}

	public function queryPK($tableId,$key) {
		return $this->getTable($tableId)->selectPK($key);
	}

	public function queryFK($tableId,$key) {
		return $this->getTable($tableId)->selectFK($key);
	}

	public function queryFKs($tableId,array $keys) {
		return $this->getTable($tableId)->selectFKs($keys);
	}

	public function queryFKOrderBy($tableId,$key,$orderBy) {
		if (isset($orderBy))
		{
			return $this->getTable($tableId)->selectFKOrderBy($key,$orderBy);
		}
		else
		{
			return $this->queryFK($tableId,$key);
		}
	}

	public function queryFKOrderByWhere($tableId,$key,$orderBy,$where)
	{
		return $this->getTable($tableId)->selectFKOrderByWhere($key,$orderBy,$where);
	}

	public function queryAll($tableId) {
		$select = $this->getTable($tableId)->select();
		return $this->getTable($tableId)->fetchAll($select);
	}

	public function queryAllOrderBy($tableId,$orderBy) {
		if (isset($orderBy))
		{
			$select = $this->getTable($tableId)->select()->order($orderBy);
			return $this->getTable($tableId)->fetchAll($select);
		}
		else
		{
			return $this->queryAll($tableId);
		}
	}

	public function countAll($tableId,$where) {
		$select = $this->getTable($tableId)->select()->from($this->getTable($tableId), array('count(*) as amount'))->where($where );
		$rows = $this->getTable($tableId)->fetchAll($select);
		return($rows[0]->amount);
	}


	public function query($tableId,$keyword)
	{
		return $this->getTable($tableId)->selectWhere( $this->getTable($tableId)->getPk() . ' LIKE \'%' . $keyword . '%\' OR nif LIKE \'%' . $keyword . '%\'' );
	}

	public function queryIDandFK($tableId,$name,$parentId)
	{
		$where = $this->getTable($tableId)->getID() . '=\'' . $name . '\' AND '.$this->getTable($tableId)->getFK().'= \'' . $parentId . '\'';
			
		return $this->getTable($tableId)->selectWhere( $where );
	}

	public function queryWhere($tableId,$where)
	{
		return $this->getTable($tableId)->selectWhere( $where );
	}

	public function queryWhereOrderBy($tableId,$where, $orderBy)
	{
		return $this->getTable($tableId)->selectWhereOrderBy(  $where , $orderBy );
	}

	public function create($tableId,array $data)
	{
		return $this->getTable($tableId)->insertData($data);
	}

	public function update($tableId,array $data, $id) {
		return $this->getTable($tableId)->updateData($data,$id);
	}

	public function delete($tableId,$id){
			
		/* create where clause */
		$ttable = $this->getTable($tableId);
		$where = $ttable->getAdapter()->quoteInto($ttable->getPK() . ' = ?', $id);

			
		return $ttable->delete($where);
	}

	public function deleteWhere($tableId,array $data){

		/* create where clause */
		$ttable = $this->getTable($tableId);
		$sqlwhere = '';
		$notfirst=1;
		foreach ($data as $key => $value)
		{
			if ($notfirst)
			{
				$notfirst=0;
			}
			else
			{
				$sqlwhere .=  ' AND ';
			}
			$sqlwhere .= $key.' = \''.$value . '\'';
		}
			
		$where = $ttable->getAdapter()->quoteInto($sqlwhere);

		return $ttable->delete($where);
	}

	public function  queryToDisplay($tabla,$id=null,$where=null)
	{
		if (isset($where))
		{
			$data = $this->getTable($tabla)->selectWhere($where);
		}
		else if (isset($id))
		$data = $this->queryFK($tabla,$id);
		else
		$data = $this->queryAll($tabla);
		$ret = array();
		$pk = $this->getTable($tabla)->getPK();
		$id = $this->getTable($tabla)->getId();
		Foreach($data as $res )
		{
			$ret[$res[$id]] = $res[$pk];
		}
		return $ret;

	}

	public function  queryToDisplayValues($tabla,$id=null)
	{
		if (isset($id))
		$data = $this->queryFK($tabla,$id);
		else
		$data = $this->queryAll($tabla);
		$ret = array();
		$pk = $this->getTable($tabla)->getPK();
		$id = $this->getTable($tabla)->getId();
		Foreach($data as $res )
		{
			$ret[$res[$pk]] = $res[$pk];
		}
		return $ret;

	}

}
?>