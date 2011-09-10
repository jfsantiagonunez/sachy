
<?php
	class Form_Marca extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'marca', array(
					'label' => 'Marca',
					'required' => true
			));
			
			
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
	}
?>