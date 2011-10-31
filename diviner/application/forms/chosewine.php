<?php
	class Form_ChooseWine extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());
		
				
			$listp = array();
			$listp['0'] = 'Me';
			$listp['1'] = 'Group';
			$listp['2'] = 'Couple';
			$listp['3'] = 'Friends';
			$listp['4'] = 'Colleagues';
			$listp['5'] = 'Mix';
		
		
			$listt = array();
			$listt['0'] = 'Red';
			$listt['1'] = 'White';
			$listt['2'] = 'Rose';
			$listt['3'] = 'Sherry';
			$listt['4'] = 'Port';
			$listt['5'] = 'Sweet';
			$listt['6'] = 'Bubbles';
			$listt['7'] = 'Mix';
			$listt['8'] = 'Surprise';
				
			$this->addElement('select','people', array(
																'label' => 'People',
																'multiOptions'             => $listp,
											    				'required'                 => true,
											    				'registerInArrayValidator' => false));
				
			$this->addElement('select','winetype', array(
													'label' => 'Wine Type',
													'multiOptions'             => $listt,
								    				'required'                 => true,
								    				'registerInArrayValidator' => false));
		
		
			$listf = array('0'=>'Salty','1'=>'Sweet','2'=>'Sour','3'=>'Bitter','4'=>'No Food');
				
			$this->addElement('select','food', array(
																'label' => 'Food',
																'multiOptions'             => $listf,
											    				'required'                 => true,
											    				'registerInArrayValidator' => false));
				
			$liste = array('0'=>'Dinner','1'=>'Formal','2'=> 'BBQ','3' => 'Picnic','4'=> 'Holiday',
									'5' => 'Special','6' => 'Gift');
				
			$this->addElement('select','event', array(
										'label' => 'Event',
										'multiOptions'             => $liste,
					    				'required'                 => true,
					    				'registerInArrayValidator' => false)
			);
		
				
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