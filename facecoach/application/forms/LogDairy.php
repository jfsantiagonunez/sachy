<?php
	class Form_LogDairy extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('textarea', 'logText', array(
					'label' => 'Log ' . date('Y-m-d h:m:s') ,
					'required' => true
			));
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
	}
?>