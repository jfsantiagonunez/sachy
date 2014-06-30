<!--  See javascripts on utils.js -->

<?php
class MfmHelperView extends Zend_View
{
	public function foo($string)
	{
		echo $string;
	}
	
	function baseUrl() {
		return substr($_SERVER['PHP_SELF'], 0, -9);
	}
	
	public function PrintTestCaseCriteriaName(array $dataItem)
	{
		
		if (!isset($dataItem['target']))
		{
			return ' >= 100%';
		}
		$name = '';
		if ($dataItem['comparison']=='0')
			$name .= ' >= ';
		else
			$name .= ' < ';
		$name .= $dataItem['target'];
		if ($dataItem['percentage']=='0')
			$name .= ' % ';
		return $name;
	}

	public function PrintSelectableForm($level,
																			$pk,
																			array $fkreferals,
																			$fkkey,
																			$pkinReferalTable,
																			array $dataItem)
	{
						
			$optionsel = array(	'controller' => 'Configuration',
					'action' => 'editajax',
					 $pk => $dataItem[$pk],
					'level' => $level,
					'attrib' => $fkkey);
				
			$linkselect = $this->url($optionsel, 'default', true);
		
			echo  '<form action="'.$linkselect.'"><select name="'.$fkkey.'" class="optionselect" id="'.$dataItem[$pk].'">';
			foreach ( $fkreferals as $fkreferal )
			{
					
				echo '<option value="'.$fkreferal[$pkinReferalTable].'"';
				if ($dataItem[$fkkey]==$fkreferal[$pkinReferalTable])
				{
					echo ' selected="selected"';
				}
				echo '>';
				if ( $fkkey == 'fkidCriteria' )
					echo $this->PrintTestCaseCriteriaName($fkreferal);
				else
					echo $fkreferal['name'];
				echo '</option>';
			}
			echo  '</select></form>';
	}
	
	
	public function PrintFavoriteStar(array $dataItem,
																			array $link )
	{
		if ( !isset($dataItem['favorite']))
		{
			return;
		}
		
		$link['action']='query';
		$project=$dataItem['fkidProject'];
		
		$action = 'markfavoriteajax';
		if ($dataItem['favorite']=='1')
			$action = 'un'.$action;
		
		$linkToPush = array(	'controller' => 'Configuration',
				'action' => $action,
				'url' => urlencode(serialize($link)),
				'fkidProject' => $project,
				'level' => $dataItem['level'],
				'id'=>$dataItem['id']
		);
			
	
		//echo serialize($link);
			
		// Adding Header
		echo '<a class="favoriteStar" href="'. $this->url($linkToPush,'default', true) . '"  target="_blank">';
		
		$style = 'style="font-size:larger"';
		if ($dataItem['favorite']=='1')
			//echo '<span'.$style.'>&#9733</span>';
			echo '&#9733';
		else
				//echo '<span'.$style.'>&#9734</span>';
			echo '&#9734';
		echo '</a>';
		
	}
	
	public function PrintFormText(array $dataItem,
																$pk,
																$level ,
																$paramId,
																$initValue )
	{
		
		$linkToPush = array(	'controller' => 'Configuration',
				'action' => 'editajax',
				'level' => $level,
				$pk => $dataItem[$pk],
				'attrib'=>$paramId );
		
		if ( !empty($dataItem[$paramId]))
		{
			$initValue=$dataItem[$paramId];
		}	
		echo '<td><form action="'.$this->url($linkToPush,'default', true).'"><input type="text" name="'.$paramId.'" class="editajaxaction" value="'.$initValue.'"></form></td>';
		
	}
}
?>