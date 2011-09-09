<?php
	class Form_Evaluation extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('textarea', 'strength', array(
					'label' => 'Strength',
					'required' => false
			));

			$this->addElement('textarea', 'weakness', array(
					'label' => 'Weakness',
					'required' => false
			));
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
			
		}
	}
?>
