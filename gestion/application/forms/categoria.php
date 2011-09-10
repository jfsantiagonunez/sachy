<?php
	class Form_Categoria extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'categoria', array(
					'label' => 'Categoria Producto',
					'required' => true
			));
			
			
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
	}
?>