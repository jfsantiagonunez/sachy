
<?php
	class Form_Descuento extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'descuento', array(
					'label' => 'Descuento',
					'required' => true
			));
			
			
			$list = array();	
			$list['0'] = 'Toda Factura';
			$list['1'] = 'Por Calidad';
			$list['2'] = 'Otras Calidades';
				
			$this->addElement('select','tipoDescuento', array(
					'label' => 'Descuentos',
					'multiOptions'             => $list,
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);
			
			$this->addElement('text', 'calidad', array(
					'label' => 'Calidad (SOLO Para Descuentos Por Calidad)!!!',
					'required' => false
			));
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
			
			$this->addElement('text', 'descripcion', array(
					'label' => 'VIEJA REFERENCIA A DESCRIPCION . NO USAR<.SOLO PARA VISUALIZAR',
					'required' => false
			));
		}
	}
?>