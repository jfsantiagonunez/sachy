<?php

require_once(APPLICATION_PATH . '/models/ModelBase.php');

	class ModelLeitmotif extends ModelBase {

		protected $_table;
		protected $_tablemotif;
		protected $_TblVinculo;
		protected $_TblShare;
		public 	  $_newleitmotiftoken = 'leitmotifNew-';
		protected $lenghttitle = 35;

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
		
		public function queryLeitmotifPerUser($level,$idUser)
		{
			
			$tableId = 'TblLeitmotif';
			
			$where = 'lm.level = \''.$level.'\' ';
			$where .= 'AND vincu.idUser = \''.$idUser.'\'';
			
			$query = $this->getTable($tableId)
				->select()
				->from(array('lm' => $tableId ),
				array('idLeitmotif','leitmotif','complete','urgency','owner','complete','urgency','priority','importance',
						'idShare','idUp','level','fecha') )
				->join(array('vincu' => 'TblVinculo'), 'vincu.idShare = lm.idShare AND '. $where , array('permiso','profile') )
				->order('lm.priority')
				->setIntegrityCheck(false);
			
			return $this->getTable($tableId)->fetchAll($query);
		}
		
		public function queryChildrenLeitmotifs($idLeitmotif)
		{
			$tableId = 'TblLeitmotif';
				
			$where = 'lm.idUp = \''.$idLeitmotif .'\'';
				
				
			$query = $this->getTable($tableId)
				->select()
				->from(array('lm' => $tableId ),
				array('idLeitmotif','leitmotif','complete','urgency','owner','complete','urgency','priority','importance',
										'idShare','idUp','level','fecha') )
				->where($where)
				->order('lm.priority');
				
			return $this->getTable($tableId)->fetchAll($query);
		}
		
		public function createUpdateLeitmotif(array $data)
		{
			//print_r($data);
			// See leimotif.js
			$idLeitmotif = $data['idLeitmotif'];
			
			$textLeitmotif = urldecode($data['textLeitmotif']);
			$data['leitmotif'] = $textLeitmotif;
			unset($data['textLeitmotif']);
			
			// Maybe it is new
			
			$tokensize = strlen($this->_newleitmotiftoken);
			$new = false;
			$level = '0';
			
			if (strncmp($idLeitmotif,$this->_newleitmotiftoken,$tokensize) == 0 )
			{
				// It is a new element
				// We need to add stuff to the data:
				$data['fecha'] = date('Y-m-d h:m:s');
				$extract = $tokensize - strlen($idLeitmotif);  // Yes, it should be -1 or -2...
				$level = substr($idLeitmotif, $extract );
			
				$auth = Zend_Auth::getInstance();
				if($auth->hasIdentity())
				{
					$data['owner'] = $auth->getIdentity()->idUser;
					$data['idUser'] = $auth->getIdentity()->idUser;
				}
				$data['level'] = $level;
				
				// Create a Share & Vinculo
				$data['idShare'] = $this->create('idShare', $data);
				$this->create('idVinculo',$data);
								
				$data['idLeitmotif'] = $this->create('idLeitmotif', $data);
				
				$new=true;

			}
			else
			{
				// It is an update
				$this->update('idLeitmotif', $data, $idLeitmotif);
			}
			
			$output = $this->buildOutputLeitmotif($data,false);

			if ($new)
			{
				$output .= $this->buildOutputLeitmotifNew($level);
			}
			print($output);
			return $output;
		}
			
		public function buildOutput(array $data,$level,$parent)
		{		
			$output = '<div class="leitmotifList" id="'.$parent.'">';
			$output .= $this->getTextTitle($level);
		
			foreach ($data as $rowdata)
			{
				$output .= $this->buildOutputLeitmotif($rowdata,false);
			}
			
			$output .= $this->buildOutputLeitmotifNew($level);
			$output .= "</div>"; //leitmotifsList

			return $output;
		}
		
		public function buildOutputLeitmotif(array $data,$isNew)
		{
			//print_r($rowdata);
			$id = $data['idLeitmotif'];
			$leitmotiftext = $data['leitmotif'];
			
			$output = '<div class="leitmotifItem" id="'.$id.'"><div class="leitmotifFrame">';
		
			// Add Text Areas
			$output .= $this->buildText($id, $leitmotiftext,$isNew);

			// Add Icons
			//
		
			$output .= '<div class="leitmotifActions"><table><tr>';
			
			// Add extra levels
			$levelnum=(int)$data['level'];
			
			if ($levelnum > 0 )
			{
				$output .= '<td><a title="Go to Upper Level" href="#" onclick="return leitmotifGotoLevel(\''.$id.'\', \'-1\');"><img src="img/icons/arrow_left.png"/></a></td>';
			}
		
			if (!$isNew)
			{
				$output .= '<td><a title="Delete" href="#" onclick="return leitmotifDelete(\''.$id.'\');"><img src="img/icons/delete.png"/></a></td>';
			
				// State			
				$output .= '<td>'.$this->getTextForComplete($data['complete']).'</td>';
			
				
				if ($levelnum < 4 )
				{
					$output .= '<td><a title="Go to Lower Level" href="#" onclick="return leitmotifGotoLevel(\''.$id.'\', \'1\');"><img src="img/icons/arrow_right.png"/></a></td>';
				}
				$output .= '</tr></table></div>'; //leitmotifActions
			}
					
			// Closing 
			$output .= '</div></div>'; // leitmotifItem
		
			return $output;
		}
			
		public function buildOutputLeitmotifNew($level)
		{
			$newitem = array (	'idLeitmotif'=>$this->_newleitmotiftoken . $level , 
								'leitmotif'=>'Add new ... ',
								'level' => $level );
		
			return $this->buildOutputLeitmotif($newitem,true);
		
		}
			
		public function buildText($id,$leitmotiftext,$isNew)
		{
				
			$lenghttitle  = strpos($leitmotiftext,"<br>");
			if ($lenghttitle===false)
			{
				// No break line found. Use total lenght
				$lenghttitle = strlen($leitmotiftext);
			}
				
			if ( $lenghttitle > $this->lenghttitle )
			{
				$lenghtttile = $this->lenghttitle;
			}
				
			// Show title
			$output = '<div class="leitmotifText" onClick="return leitmotifSelect( \''.$id.'\')";>';
				
			$output .= '<div class="leitmotifTitle" id="'.$id.'-Title" style="display:block" >'.substr($leitmotiftext,0,$lenghttitle) . '</div>';
			if ($isNew)
			{
				$leitmotiftext = '';
			}
			$output .= '<div class="leitmotifTextEdit" id="'.$id.'-Edit" style="display:none" contenteditable="true" onBlur="return leitmotifUpdate(\''.$id.'\');">' . $leitmotiftext . '</div>';
			
			$output .= "</div>"; //leitmotifText
			
			return $output;
			
		}
		
		public function getTextTitle($value)
		{
			$output  ='';
			switch ($value)
			{
			/*0 - To Do
			1 - In Progress
				2 - Done
			3 - Abandoned
			4 - Not Need*/
			case '0' : $output .= 'Mision';break;
					case '1' : $output .= 'Goals'; break;
					case '2' : $output .= 'Strategies'; break;
				case '3' : $output .= 'Tasks'; break;
					case '4' : 
				default :
				$output .= 'Subtasks'; break;
			}
		
			return $output;
				
		}
		
		public function getTextForComplete($value)
		{
			$output  ='';
			switch ($value)
			{
			/*0 - To Do
				 	1 - In Progress
			2 - Done
				3 - Abandoned
					4 - Not Need*/
				case '0' : $output .= 'To Do';break;
				case '1' : $output .= 'In Progress'; break;
				case '2' : $output .= 'Done'; break;
				case '3' : $output .= 'Abandoned'; break;
				case '4' : $output .= 'Not Need'; break;
			}
			return $output;
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