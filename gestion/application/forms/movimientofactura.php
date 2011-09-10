<?php
	class Form_MovimientoFactura extends Zend_Form {

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
			
			$this->addElement('text', 'cantidad', array(
					'label' => 'Cantidad',
					'required' => true
			));
			
			$list = array();
			$list['1'] = 'salida o factura';
			$list['-1'] = 'entrada o abono';
		
			$list2 = array();
			$list2['0'] = '0';
			$list2['100'] = '100';
			
       		$this->addElement('select','salida', array(
					'label' => 'Salida',
					'multiOptions'             => $list,
    				'required'                 => true,
    				'registerInArrayValidator' => false));
			
       		$this->addElement('select','descuento', array(
					'label' => 'Descuento',
					'multiOptions'             => $list2,
    				'required'                 => true,
    				'registerInArrayValidator' => false));
       		
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
		
		public function initWith($id,$value)
		{
			
		}
	}
?>