<?php
	class Form_Wine extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());
		
			$this->addElement('text', 'winename', array('label' => 'Wine Name','required' => true ));
			$this->addElement('text', 'producer', array('label' => 'Producer','required' => true ));
			$this->addElement('text', 'grape', array('label' => 'Grape(s)','required' => true));
			$this->addElement('text', 'year', array('label' => 'Year','required' => true));
			$this->addElement('text', 'region', array('label' => 'Region','required' => true));
			//  'labellink' varchar(45) DEFAULT 'labelunknown',
			$this->addElement('text', 'price', array('label' => 'Price','required' => true));
				
			$this->addElement('hidden', 'linea1', array('label' => ''));
			$this->addElement('hidden', 'linea', array('label' => 'People'));
			$this->addElement('text',  'people_me' , array('label' => 'Me','required' => true));
			$this->addElement('text',  'people_group' , array('label' => 'Group','required' => true));
			$this->addElement('text',  'people_couple' , array('label' => 'Couple','required' => true));
			$this->addElement('text',  'people_friends' , array('label' => 'Friends','required' => true));
			$this->addElement('text',  'people_colleagues' , array('label' => 'Colleagues','required' => true));
			$this->addElement('text',  'people_mix' , array('label' => 'Mix','required' => true));
				
			$this->addElement('hidden', 'linea2', array('label' => ''));
			$this->addElement('hidden', 'linea22', array('label' => 'Food'));
			$this->addElement('text',  'food_salty' , array('label' => 'Salty','required' => true));
			$this->addElement('text',  'food_sweet' , array('label' => 'Sweet','required' => true));
			$this->addElement('text',  'food_sour' , array('label' => 'Sour','required' => true));
			$this->addElement('text',  'food_bitter' , array('label' => 'Bitter','required' => true));
			$this->addElement('text',  'food_nofood', array('label' => 'No Food','required' => true));
				
				
			$this->addElement('hidden', 'linea3', array('label' => ''));
			$this->addElement('hidden', 'linea33', array('label' => 'Event'));
			$this->addElement('text',  'event_dinner' , array('label' => 'Dinner','required' => true));
			$this->addElement('text',  'event_formal' , array('label' => 'Formal','required' => true));
			$this->addElement('text',  'event_bbq' , array('label' => 'BBQ','required' => true));
			$this->addElement('text',  'event_picnic' , array('label' => 'Picnic','required' => true));
			$this->addElement('text',  'event_holiday' , array('label' => 'Holiday','required' => true));
			$this->addElement('text',  'event_special' , array('label' => 'Special','required' => true));
			$this->addElement('text',  'event_gift' , array('label' => 'Gift','required' => true));
				
			$this->addElement('hidden', 'linea4', array('label' => ''));
			$this->addElement('hidden', 'linea44', array('label' => 'Wine Type'));
			$list = array();
			$list['0'] = 'Red';
			$list['1'] = 'White';
			$list['2'] = 'Rose';
			$list['3'] = 'Sherry';
			$list['4'] = 'Port';
			$list['5'] = 'Sweet';
			$list['6'] = 'Bubbles';
				
			$this->addElement('select','winetype', array(
										'label' => 'Wine Type',
										'multiOptions'             => $list,
					    				'required'                 => true,
					    				'registerInArrayValidator' => false)
			);
			$this->addElement('text',  'winetype_mix' , array('label' => 'Mix','required' => true));
			$this->addElement('text',  'winetype_surpriseme' , array('label' => 'Surprise','required' => true));
				
			$this->addElement('submit', 'submit', array(
							'label' => 'Save'
			));
			$this->setDecorators(array(
		    			'FormElements',
			array('HtmlTag', array('tag' => 'table')),
		    			'Form'
			));
			$this->setElementDecorators(array(
		  	  			'ViewHelper',
		    			'Errors',
			array(array('data' => 'HtmlTag'), array('tag' => 'td')),
			array('Label', array('tag' => 'td')),
			array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
			));
		}
	}
?>