
<?php
	class Form_Deposito extends Zend_Form {

		public function init() {
			$this->setMethod('post');
			$this->setAction($this->getView()->url());

			$this->addElement('text', 'tienda', array(
					'label' => 'Deposito',
					'required' => true
			));
			
			require_once(APPLICATION_PATH . '/models/ModelCliente.php');
			$model = new ModelCliente();
			
			$tiendas = $model->queryToDisplay('TblCliente','idCliente','tienda = \'1\'');
					
			$this->addElement('select','idCliente', array(
					'label' => 'Tienda',
					'multiOptions'             => $tiendas,
    				'required'                 => true,
    				'registerInArrayValidator' => false)
			);
			
			$this->addElement('submit', 'submit', array(
					'label' => 'Save'
			));
		}
	}
?>