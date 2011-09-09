<?php
	class Form_Vision extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('textarea', 'descVision', array(
					'label' => 'Vision',
					'required' => true
			));
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
	}
?>