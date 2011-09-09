<?php
	class Form_User extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'firstname', array(
					'label' => 'First Name',
					'required' => true
			));
			
			$this->addElement('text', 'lastname', array(
					'label' => 'Last Name',
					'required' => true
			));
			
			$this->addElement('text', 'username', array(
					'label' => 'Username',
					'required' => true
			));

			$this->addElement('text', 'password', array(
					'label' => 'Password',
					'required' => true
			));

			$list = array();
			$list['0'] = 'User';
			$list['1'] = 'Coacher';
			
			$this->addElement('select','userprofile', array(
					'label' => 'User Profile',
					'multiOptions'             => $list,
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
	}
?>