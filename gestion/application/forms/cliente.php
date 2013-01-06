<?php

 
class HrSeparator extends Zend_Form_Decorator_HtmlTag
{
    protected $_placement = self::APPEND;
    protected $_tag = 'hr';
}
//$field = new Zend_Form_Element_Text('fieldName');
//$field->addDecorator(new Personal_Decorator_HrSeparator())

	class Form_Cliente extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'nombre', array(
					'label' => 'Nombre',
					'required' => true
			));
			
			$this->addElement('text', 'nif', array(
					'label' => 'NIF',
					'required' => true
			));
			
			$this->addElement('text', 'codigocliente', array(
					'label' => 'Codigo Contable Cliente 4300.xxxx',
					'required' => false
			));
			
			$list = array();
			$list['0'] = 'Cliente';
			$list['1'] = 'Cliente y Proveedor';

			$this->addElement('select','proveedor', array(
					'label' => 'Cliente o Proveedor',
					'multiOptions'             => $list,
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);
			$this->addElement('text', 'numrefproveedor', array(
					'label' => 'Si Cliente, numero proveedor para ellos',
					'required' => false
			));
			
			$this->addElement('text', 'telefono', array(
					'label' => 'Telefono',
					'required' => false
			));
			
			$this->addElement('text', 'fax', array(
					'label' => 'Fax',
					'required' => false
			));
			
			$this->addElement('text', 'email', array(
					'label' => 'Email',
					'required' => false
			));
			$this->addElement('hidden', 'linea1', array(
					'label' => ''
			));
			$this->addElement('hidden', 'linea', array(
					'label' => 'Direccion Albaran'
			));
			// Direccion Albaran
			$this->addElement('text', 'direccion', array(
					'label' => 'Calle',
					'required' => true
			));
			
			$this->addElement('text', 'ciudad', array(
					'label' => 'Ciudad',
					'required' => true
			));
			
			$this->addElement('text', 'codigopostal', array(
					'label' => 'Codigo Postal',
					'required' => true
			));
			
			// Direccion Factura
			$this->addElement('hidden', 'linea22', array(
					'label' => ''
			));
			$this->addElement('hidden', 'linea2', array(
					'label' => 'Direccion Factura (si es diferente al albaran)'
			));
			$this->addElement('text', 'direccionfact', array(
					'label' => 'Calle',
					'required' => false
			));
			
			$this->addElement('text', 'ciudadfact', array(
					'label' => 'Ciudad',
					'required' => false
			));
			
			$this->addElement('text', 'codigopostalfact', array(
					'label' => 'Codigo Postal',
					'required' => false
			));
			
			// Condiciones Pago
			$this->addElement('hidden', 'linea33', array(
					'label' => ''
			));
			$this->addElement('hidden', 'linea3', array(
					'label' => 'Condiciones Pago'
			));
			
		
			require_once(APPLICATION_PATH . '/models/ModelBase.php');
			$model = new ModelBase();
			$listtipopago = $model->getTipoPago();
						
			$this->addElement('select', 'condicionespago', array(
					'label' => 'Tipo Pago',
					'multiOptions'             => $listtipopago,
    				'required'                 => false,
    				'registerInArrayValidator' => false)
			);
			
			$vencimientos = $model->getDiasPago();
			
			$this->addElement('select', 'vencimiento', array(
					'label' => 'Vencimiento',
					'multiOptions'             => $vencimientos,
    				'required'                 => false,
    				'registerInArrayValidator' => false)
			);
			
			$this->addElement('text', 'banco', array(
					'label' => 'Entidad Bancaria',
					'required' => false
			));
			
			$this->addElement('text', 'sucursal', array(
					'label' => 'Sucursal',
					'required' => false
			));
			
			$this->addElement('text', 'dc', array(
					'label' => 'D.C.',
					'required' => false
			));
			
			$this->addElement('text', 'cuentabancaria', array(
					'label' => 'Numero Cuenta',
					'required' => false,
					'value' => '0000-0000-00-0000000000'
			));
			$this->addElement('submit', 'submit', array(
					'label' => 'Guardar'
			));
			
			$this->setDecorators(array(
    			'FormElements',
    			array('HtmlTag', array('tag' => 'table')),
    			'Form'
				));
				
			$this->setElementDecorators(array(
  	  			'ViewHelper',
    			'Errors',
 	  	 		array(array('data' => 'HtmlTag'), array('tag' => 'td')),
    			array('Label', array('tag' => 'td')),
   		 		array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
				));
		}
		
		public function initWith($idCliente)
		{

		}
		
	}
?>