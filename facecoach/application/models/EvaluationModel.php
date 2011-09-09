<?php

	class EvaluationModel {
		/* the dbtable model */
		protected $evaluationTbl,$stepTbl,$stepDictTbl;

		public function getTable($table) {
			/* initialize the table model */
			if ($table=='evaluation')
			{
				if($this->evaluationTbl === null) {
					require_once(APPLICATION_PATH . '/models/DbTable/TblEvaluation.php');
					$this->evaluationTbl = new DbTable_TblEvaluation();
				}
  				return $this->evaluationTbl;
			}
			else if ($table=='stepEvaluation')
			{
				if($this->stepTbl === null) {
					require_once(APPLICATION_PATH . '/models/DbTable/TblStepEvaluation.php');
					$this->stepTbl = new DbTable_TblStepEvaluation();
				}
				return $this->stepTbl;
			}
			else if ($table=='stepDict')
			{
				if($this->stepDictTbl === null) {
					require_once(APPLICATION_PATH . '/models/DbTable/TblStepEvaluationDict.php');
					$this->stepDictTbl = new DbTable_TblStepEvaluationDict();
				}
				return $this->stepDictTbl;
			}
		}

		
		public function getEvaluationsPerGoalKey($key) {
			return $this->getByFK('evaluation',$key);
		}
	
		public function getIndexActionEvaluationsPerGoalKey($key) {
			
			$query = $this->getTable('stepEvaluation')
					->select()
					->from(array('TblEvaluation' => 'TblEvaluation' , 
							array('idGoal','timestamp'
									//, 'weaknesses' => new Zend_Db_Expr('SELECT COUNT(NULLIF(weakness, \'\')) FROM TblStepEvaluation where tblstepevaluation.idEvaluation = \'' . $key . '\'')
									) ) )
					->join(array('users' => 'TblUser'), 'users.idUser = TblEvaluation.idUser AND TblEvaluation.idGoal = \'' . $key . '\'', array('username') )
					->join(array('wks' => 'weaknesscounter'), 'TblEvaluation.idEvaluation = wks.idEvaluation' , array('weaknesses'))
					->join(array('sts' => 'strengthscounter'), 'TblEvaluation.idEvaluation = sts.idEvaluation ' , array('strengths' ))
					->group('TblEvaluation.idEvaluation')
					->setIntegrityCheck(false);
			return $this->getTable('evaluation')->fetchAll($query);
			
		}
		
		public function getIndexActionEvaluationsPerGoalKeys($keys) {
			
			$query = $this->getTable('stepEvaluation')
					->select()
					->from(array('TblEvaluation' => 'TblEvaluation' , 
							array('idGoal','timestamp'
									//, 'weaknesses' => new Zend_Db_Expr('SELECT COUNT(NULLIF(weakness, \'\')) FROM TblStepEvaluation where tblstepevaluation.idEvaluation = \'' . $key . '\'')
									) ) )
					->join(array('users' => 'TblUser'), 'users.idUser = TblEvaluation.idUser AND (' . $keys . ')', array('username') )
					->join(array('wks' => 'weaknesscounter'), 'TblEvaluation.idEvaluation = wks.idEvaluation' , array('weaknesses'))
					->join(array('sts' => 'strengthscounter'), 'TblEvaluation.idEvaluation = sts.idEvaluation ' , array('strengths' ))
					->group('TblEvaluation.idEvaluation')
					->setIntegrityCheck(false);
			return $this->getTable('evaluation')->fetchAll($query);
			
		}
		
		public function getIndexActionStepEvaluationsPerIdEvaluation($key) {
			
			$query = $this->getTable('stepEvaluation')
					->select()
					->from(array('TblStepEvaluation' => 'TblStepEvaluation' , array('keyStep','idStep','strength','weakness') ) )
					->join(array('dict' => 'TblStepEvaluationDict'), 'dict.idStep = TblStepEvaluation.idStep AND TblStepEvaluation.idEvaluation = \'' . $key . '\'', array('nameStep') )
					->setIntegrityCheck(false);
			return $this->getTable('evaluation')->fetchAll($query);
		}
		
		
		public function getIndexEvaluationsAsCoacherPerGoalKey($idUser )
		{
			
		}
		
		
		
		
		public function getEvaluation($key) {
			return $this->getByPK('evaluation',$key);
		}
		
		public function getEvaluationsStepsPerEvaluationKey($key)
		{
			return $this->getByFK('stepEvaluation',$key);
		}
		
		public function getEvaluationStep($key)
		{
			return $this->getByPK('stepEvaluation',$key);
		}
		
		public function getEvaluationsPerUserKey($key) {
			return $this->getByFK2('evaluation',$key);
		}

		public function  getEvaluationsPerGoalKeys($keyString)
		{
			$table = $this->getTable('evaluation');
			$select = $table->select()->where($keyString);
			return $table->fetchAll($select);
		}
		
		public function  getStepEvaluationsPerGoalKey($keyString)
		{
			$table = $this->getTable('stepEvaluation');
			$select = $table->select()->where($keyString);
			return $table->fetchAll($select);
		}
		
		public function createEvaluationId($idGoal,$idUser)
		{
			$data = array();
			$data ['idUser'] = $idUser;
			$data ['idGoal'] = $idGoal;
			$data['timestamp'] = Zend_Date::now()->toString("YYYY:MM:dd HH:mm");;
			return $this->insert('evaluation',$data);
		}
		
		

		public function insertStep($data)
		{
			return $this->insert('stepEvaluation',$data);
		}
		
		public function getStepDict($step)
		{
			return $this->getByPK('stepDict',$step);	
		}
		
		public function nextStep($step)
		{
			$step++;
			$data = $this->getByPK('stepDict',$step);
			if ($data['idStep'] == $step)
			{
				return $step;
			}
			else
			{
				return 0;
			}
		}
		
		public function updateStep($data)
		{
			return $this->update('stepEvaluation',$data,$data['keyStep']);
		}
		
		public function getWeaknessPerGoal($idGoal)
		{
			$evaluations = $this->getEvaluationsPerGoalKey($idGoal);
			if ( $evaluations != null)
			{
				$theEvaluations = '(';
				$init = 0;
				foreach ($evaluations as $evaluation )
				{
					$evaluationId = $evaluation['idEvaluation'];

					if ($init==0)
					{
						$init=1;
					}
					else
					{
						$theEvaluations .= ' OR';
					}
					$theEvaluations .= ' idEvaluation = ' . $evaluationId;
								
				}
				$theEvaluations .= ' ) AND weakness!=\'\'';
	
				return $this->getStepEvaluationsPerGoalKey($theEvaluations);
			}
		}
		
		public function updateStepEvaluationWithStrategy($keyStep,$idStrategy)
		{
			$data = array();
			$data['keyStep'] = $keyStep;
			$data['idStrategy'] = $idStrategy;
			$this->updateStep($data);
		}
		
		public function deleteEvaluation($idEvaluation)
		{
			$this->delete('evaluation',$idEvaluation);
		}
		
		public function deleteStepEvaluation($keyStep)
		{
			$this->delete('stepEvaluation',$keyStep);
		}
		/// Common
		
		public function getByPK($tableId,$key){
			$table = $this->getTable($tableId);
			$select = $table->select()->where($table->getPK() . ' = ?',$key);
			return $table->fetchRow($select);
		}
		
		public function getByFK($tableId,$key){
			$table = $this->getTable($tableId);
			$select = $table->select()->where($table->getFK() . ' = ?',$key);
			return $table->fetchAll($select);
		}
		
		public function getByFK2($tableId,$key){
			$table = $this->getTable($tableId);
			$select = $table->select()->where($table->getFK2() . ' = ?',$key);
			return $table->fetchAll($select);
		}
		
		public function insert($tableId,array $data) {
			$table = $this->getTable($tableId);
			/* check if all the posted fields are valid db fiels */
			$fields = $table->info(Zend_Db_Table_Abstract::COLS);
			foreach($data as $field => $value) {
				if(!in_array($field, $fields)) {
					unset($data[$field]);
				}
			}
			try{
				$res = $table->insert($data);
				return $res;
			}
			catch(exception $e){
				return $e;
			}	
		}
		
		public function delete($tableId,$id){
			$table = $this->getTable($tableId);
			/* create where clause */
			$where = $table->getAdapter()->quoteInto( $table->getPK() . ' = ?', $id);

			/* delete user */
			return $table->delete($where);
		}
		
		public function update($tableId,array $data, $id) {
			$table = $this->getTable($tableId);
			/* check if all the posted fields are valid db fiels */
			$fields = $table->info(Zend_Db_Table_Abstract::COLS);
			foreach($data as $field => $value) {
				if(!in_array($field, $fields)) {
					unset($data[$field]);
				}
			}

			$where = $table->getAdapter()->quoteInto( $table->getPK() . ' = ?', $id);

			$res = $table->update($data, $where);
			return $res;
		}
}
	
?>