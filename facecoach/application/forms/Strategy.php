<?php
	class Form_Strategy extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'titleStrategy', array(
					'label' => 'Title',
					'required' => true
			));

			$this->addElement('textarea', 'descStrategy', array(
					'label' => 'Description',
					'required' => true
			));
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
	}
?>