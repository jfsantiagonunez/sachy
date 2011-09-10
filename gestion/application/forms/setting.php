
<?php
	class Form_Setting extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'idKey', array(
					'label' => 'Config',
					'required' => true
			));
			
			$this->addElement('text', 'value', array(
					'label' => 'Valor',
					'required' => true
			));
			
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
	}
?>