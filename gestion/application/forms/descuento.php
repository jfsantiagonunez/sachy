
<?php
	class Form_Descuento extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'descuento', array(
					'label' => 'Descuento',
					'required' => true
			));
			
			$this->addElement('text', 'descripcion', array(
					'label' => 'Indicar a que calidades es aplicable',
					'required' => false
			));
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
	}
?>