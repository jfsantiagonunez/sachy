<?php
	class Form_User extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'firstname', array(
					'label' => 'Name',
					'required' => true
			));
			
			$this->addElement('text', 'lastname', array(
					'label' => 'Surname',
					'required' => true
			));
			
			$this->addElement('text', 'username', array(
					'label' => 'UserId',
					'required' => true
			));

			$this->addElement('text', 'password', array(
					'label' => 'Password',
					'required' => true
			));
			
			require_once(APPLICATION_PATH . '/models/ModelUser.php');
			$model = new ModelUser();
						
			
			$listadmin = array();
			$listadmin['0'] = 'User';
			$listadmin['1'] = 'Admin';
			
			
			$this->addElement('select','profile', array(
					'label' => 'Profile',
					'multiOptions'             => $listadmin,
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