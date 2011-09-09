<?php
	class Form_Calendar extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'title', array(
					'label' => 'Calendar Title',
					'required' => true
			));
			
			$this->addElement('text', 'email', array(
					'label' => 'Email',
					'required' => true
			));
			
			$this->addElement('text', 'password', array(
					'label' => 'Password',
					'required' => true
			));

			$notification = array();
			$notification['email'] ='Email';
			$notification['alert'] = 'Pop-up';
			$notification['sms']= 'Sms';
			$this->addElement('select','notification', array(
					'label' => 'Notification Type',
					'multiOptions'             => $notification,
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
	}
?>