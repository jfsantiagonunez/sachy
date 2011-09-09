<?php
	class Form_Goal extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'titleGoal', array(
					'label' => 'Title',
					'required' => true
			));

			$this->addElement('textarea', 'descGoal', array(
					'label' => 'Description',
					'required' => true
			));
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
			
			$this->addElement('reset', 'cancel', array(
					'label' => 'Cancel'
			));
			
		}
	}
?>
