<?php
	class Form_Referencia extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'calidad', array(
					'label' => 'Calidad',
					'required' => true
			));
			
			$this->addElement('text', 'color', array(
					'label' => 'Color',
					'required' => true
			));

			$this->addElement('text', 'tipoenvase', array(
					'label' => 'Tipo Envase',
					'required' => true
			));
			
			$this->addElement('text', 'precio', array(
					'label' => 'Precio',
					'required' => true
			));
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
	}
?>